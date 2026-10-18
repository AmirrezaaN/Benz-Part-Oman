<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(): View
    {
        return view('admin.banners.index', ['banners' => Banner::query()->orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $banner = new Banner($this->validated($request));
        if ($request->hasFile('image')) {
            $banner->image = $this->images->store($request->file('image'), 'banners', 1920);
        }
        $banner->save();
        ActivityLog::record('banner.created', $banner, 'ساخت اسلاید «'.$banner->title.'»');

        return redirect()->route('admin.banners.index')->with('success', 'اسلاید ساخته شد.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $banner->fill($this->validated($request));
        if ($request->hasFile('image')) {
            $this->images->delete($banner->image);
            $banner->image = $this->images->store($request->file('image'), 'banners', 1920);
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($banner->image);
            $banner->image = null;
        }
        $banner->save();
        ActivityLog::record('banner.updated', $banner, 'ویرایش اسلاید «'.$banner->title.'»');

        return redirect()->route('admin.banners.index')->with('success', 'اسلاید ویرایش شد.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->images->delete($banner->image);
        $banner->delete();
        ActivityLog::record('banner.deleted', null, 'حذف اسلاید «'.$banner->title.'»');

        return back()->with('success', 'اسلاید حذف شد.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'badge' => ['nullable', 'string', 'max:60'],
            'button_text' => ['nullable', 'string', 'max:40'],
            // Only site-relative paths or http(s) links (blocks javascript: URLs)
            'button_url' => ['nullable', 'string', 'max:255', 'regex:/^(\/[^\/]|https?:\/\/)/'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], ['button_url.regex' => 'لینک باید با / یا http شروع شود.'], [
            'title' => 'عنوان', 'subtitle' => 'زیرعنوان', 'badge' => 'برچسب', 'button_text' => 'متن دکمه',
            'button_url' => 'لینک دکمه', 'sort_order' => 'ترتیب', 'image' => 'تصویر',
        ]);

        unset($data['image']);

        return $data + ['sort_order' => $data['sort_order'] ?? 0, 'is_active' => $request->boolean('is_active')];
    }
}
