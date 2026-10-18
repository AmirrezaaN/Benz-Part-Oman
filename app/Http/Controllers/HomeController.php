<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::query()->active()->get();

        $brands = Brand::query()->active()->ordered()
            ->with(['carModels.chassis:id,car_model_id,code,slug,year_from,year_to'])
            ->withCount(['products' => fn ($q) => $q->active()])
            ->get();

        $categories = Category::query()->active()->whereNull('parent_id')->ordered()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->get();

        $featured = Product::query()->active()->withCardData()->where('is_featured', true)->latest('id')->take(12)->get();
        $latest = Product::query()->active()->withCardData()->latest('id')->take(12)->get();
        $popular = Product::query()->active()->withCardData()->orderByDesc('views')->take(12)->get();

        // One product row (swiper) per category that has products
        $categoryRows = $categories->where('show_on_home', true)->where('products_count', '>', 0)->take(4)
            ->map(fn (Category $category) => [
                'category' => $category,
                'products' => Product::query()->active()->withCardData()
                    ->whereIn('category_id', $category->selfAndChildrenIds())->latest('id')->take(10)->get(),
            ]);

        $stats = Cache::remember('home_stats', 600, fn () => [
            'products' => Product::query()->active()->count(),
            'brands' => Brand::query()->active()->count(),
            'categories' => Category::query()->active()->count(),
        ]);

        // Brand → model → chassis tree for the "find parts for your car" widget
        $vehicleTree = $brands->map(fn (Brand $b) => [
            'slug' => $b->slug,
            'name' => $b->name,
            'models' => $b->carModels->map(fn ($m) => [
                'slug' => $m->slug,
                'name' => $m->name,
                'chassis' => $m->chassis->map(fn ($c) => ['slug' => $c->slug, 'name' => $c->label()])->values(),
            ])->values(),
        ])->values();

        return view('home', compact('banners', 'brands', 'categories', 'featured', 'latest', 'popular', 'categoryRows', 'stats', 'vehicleTree'));
    }
}
