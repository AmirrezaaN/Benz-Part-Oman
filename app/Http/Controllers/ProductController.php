<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Chassis;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProductController extends Controller
{
    public const FILTER_KEYS = ['q', 'category', 'brand', 'model', 'chassis', 'year', 'country', 'condition', 'in_stock', 'sort'];

    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, ['category' => $category->slug], $category->name);
    }

    public function brand(Request $request, Brand $brand): View
    {
        abort_unless($brand->is_active, 404);

        return $this->listing($request, ['brand' => $brand->slug], 'قطعات '.$brand->displayName());
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        // Count one view per product per session
        $viewed = session('viewed_products', []);
        if (! in_array($product->id, $viewed, true)) {
            $product->increment('views');
            session()->push('viewed_products', $product->id);
        }

        $product->load(['brand', 'category.parent', 'chassis.carModel.brand', 'images']);

        $related = Product::query()->active()->withCardData()
            ->whereKeyNot($product->id)
            ->where(function ($q) use ($product) {
                $q->whereHas('chassis', fn ($c) => $c->whereIn('chassis.id', $product->chassis->pluck('id')))
                    ->orWhere('category_id', $product->category_id);
            })
            ->latest('id')->take(10)->get();

        return view('products.show', compact('product', 'related'));
    }

    protected function listing(Request $request, array $preset = [], ?string $title = null): View
    {
        $filters = array_merge(
            array_filter(Arr::only($request->query(), self::FILTER_KEYS), fn ($v) => is_string($v) && $v !== ''),
            $preset
        );
        if (isset($filters['q'])) {
            $filters['q'] = mb_substr($filters['q'], 0, 100);
        }

        $products = Product::query()->active()->withCardData()->filter($filters)
            ->paginate(16)->withQueryString();

        $brands = Brand::query()->active()->ordered()->with('carModels:id,brand_id,name,slug')->get();
        $categories = Category::query()->active()->whereNull('parent_id')->ordered()->with('children')->get();

        $chassisOptions = collect();
        if (! empty($filters['model'])) {
            $chassisOptions = Chassis::query()->whereHas('carModel', fn ($q) => $q->where('slug', $filters['model']))->orderBy('code')->get();
        } elseif (! empty($filters['brand'])) {
            $chassisOptions = Chassis::query()->whereHas('carModel.brand', fn ($q) => $q->where('slug', $filters['brand']))->orderBy('code')->get();
        }

        return view('products.index', [
            'products' => $products,
            'filters' => $filters,
            'preset' => $preset,
            'title' => $title ?? (isset($filters['q']) ? 'نتایج جستجو برای «'.$filters['q'].'»' : 'همه قطعات'),
            'brands' => $brands,
            'categories' => $categories,
            'chassisOptions' => $chassisOptions,
            'activeCount' => count(array_diff_key($filters, array_flip(['sort']))),
        ]);
    }
}
