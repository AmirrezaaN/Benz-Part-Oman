<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(): View
    {
        return view('admin.brands.index', [
            'brands' => Brand::query()->ordered()->withCount(['products', 'carModels'])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.form', ['brand' => new Brand(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $brand = new Brand($this->validated($request));
        if ($request->hasFile('logo')) {
            $brand->logo = $this->images->store($request->file('logo'), 'brands', 400);
        }
        $brand->save();
        Cache::forget('home_stats');
        ActivityLog::record('brand.created', $brand, 'ساخت برند «'.$brand->name.'»');

        return redirect()->route('admin.brands.index')->with('success', 'برند ساخته شد.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.form', ['brand' => $brand]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->fill($this->validated($request));
        if ($request->hasFile('logo')) {
            $this->images->delete($brand->logo);
            $brand->logo = $this->images->store($request->file('logo'), 'brands', 400);
        } elseif ($request->boolean('remove_logo')) {
            $this->images->delete($brand->logo);
            $brand->logo = null;
        }
        $brand->save();
        ActivityLog::record('brand.updated', $brand, 'ویرایش برند «'.$brand->name.'»');

        return redirect()->route('admin.brands.index')->with('success', 'برند ویرایش شد.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'این برند دارای محصول است و قابل حذف نیست. می‌توانید آن را غیرفعال کنید.');
        }

        $this->images->delete($brand->logo);
        $brand->delete();
        Cache::forget('home_stats');
        ActivityLog::record('brand.deleted', null, 'حذف برند «'.$brand->name.'»');

        return back()->with('success', 'برند و مدل‌ها/شاسی‌های آن حذف شد.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'name_fa' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], ['name' => 'نام انگلیسی', 'name_fa' => 'نام فارسی', 'sort_order' => 'ترتیب', 'logo' => 'لوگو']);

        unset($data['logo']);

        return $data + [
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }
}
