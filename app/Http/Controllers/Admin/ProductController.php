<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['brand:id,name', 'category:id,name', 'images'])
            ->withCount('chassis')
            ->search($request->query('q'))
            ->when($request->query('category'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->query('brand'), fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->query('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->query('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->query('status') === 'featured', fn ($q) => $q->where('is_featured', true))
            ->when($request->query('stock') === 'low', fn ($q) => $q->whereNotNull('quantity')->where('quantity', '<=', 2))
            ->latest('id')
            ->paginate(20)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'brands' => Brand::query()->ordered()->get(['id', 'name']),
            'categories' => Category::query()->ordered()->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', $this->formData(new Product(['is_active' => true])));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = new Product($request->safe()->except(['chassis', 'images']));
            $product->created_by = $request->user()->id;
            $product->save();
            $product->chassis()->sync($request->input('chassis', []));
            $this->storeImages($request, $product);

            return $product;
        });

        Cache::forget('home_stats');
        ActivityLog::record('product.created', $product, 'ساخت محصول «'.$product->name.'»');

        return redirect()->route('admin.products.edit', $product)->with('success', 'محصول با موفقیت ساخته شد.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', $this->formData($product->load('images', 'chassis')));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $product->fill($request->safe()->except(['chassis', 'images']))->save();
            $product->chassis()->sync($request->input('chassis', []));
            $this->storeImages($request, $product);

            // Re-order images: the chosen "main" image goes first
            if ($mainId = $request->integer('main_image')) {
                $product->images()->where('id', $mainId)->update(['sort_order' => -1]);
                $product->images()->get()->values()->each(fn ($img, $i) => $img->update(['sort_order' => $i]));
            }
        });

        ActivityLog::record('product.updated', $product, 'ویرایش محصول «'.$product->name.'»');

        return back()->with('success', 'تغییرات محصول ذخیره شد.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete(); // soft delete — order/inquiry history keeps its reference
        Cache::forget('home_stats');
        ActivityLog::record('product.deleted', $product, 'حذف محصول «'.$product->name.'»');

        return redirect()->route('admin.products.index')->with('success', 'محصول حذف شد.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        $image->delete();

        return back()->with('success', 'تصویر حذف شد.');
    }

    private function storeImages(ProductRequest $request, Product $product): void
    {
        $order = (int) $product->images()->max('sort_order');
        $room = 8 - $product->images()->count();

        foreach (array_slice($request->file('images', []), 0, max(0, $room)) as $file) {
            $product->images()->create([
                'path' => $this->images->store($file, 'products'),
                'sort_order' => ++$order,
            ]);
        }
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'brands' => Brand::query()->ordered()->get(['id', 'name', 'name_fa']),
            'categories' => Category::query()->whereNull('parent_id')->ordered()->with('children')->get(),
            'chassisTree' => Brand::query()->ordered()->with('carModels.chassis')->get(),
            'selectedChassis' => old('chassis', $product->exists ? $product->chassis->pluck('id')->all() : []),
        ];
    }
}
