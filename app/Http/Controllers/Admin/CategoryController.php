<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->whereNull('parent_id')->ordered()
                ->with(['children' => fn ($q) => $q->withCount('products')])->withCount('products')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true, 'show_on_home' => true]), 'parents' => $this->parents()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = Category::query()->create($this->validated($request));
        $this->flush();
        ActivityLog::record('category.created', $category, 'ساخت بخش «'.$category->name.'»');

        return redirect()->route('admin.categories.index')->with('success', 'بخش جدید ساخته شد.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category, 'parents' => $this->parents($category)]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));
        $this->flush();
        ActivityLog::record('category.updated', $category, 'ویرایش بخش «'.$category->name.'»');

        return redirect()->route('admin.categories.index')->with('success', 'بخش ویرایش شد.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'این بخش دارای محصول است. ابتدا محصولات را به بخش دیگری منتقل کنید.');
        }

        $category->children()->update(['parent_id' => null]);
        $category->delete();
        $this->flush();
        ActivityLog::record('category.deleted', null, 'حذف بخش «'.$category->name.'»');

        return back()->with('success', 'بخش حذف شد.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNull('parent_id'), Rule::notIn([$category?->id])],
            'icon' => ['nullable', 'string', 'max:60', 'regex:/^fa-[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], ['icon.regex' => 'نام آیکون باید مثل fa-gears باشد.'], [
            'name' => 'نام بخش', 'parent_id' => 'بخش والد', 'icon' => 'آیکون', 'description' => 'توضیحات', 'sort_order' => 'ترتیب',
        ]);

        return $data + [
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'show_on_home' => $request->boolean('show_on_home'),
        ];
    }

    private function parents(?Category $except = null)
    {
        return Category::query()->whereNull('parent_id')->when($except, fn ($q) => $q->whereKeyNot($except->id))->ordered()->get(['id', 'name']);
    }

    private function flush(): void
    {
        Cache::forget('nav_categories');
        Cache::forget('home_stats');
    }
}
