@extends('admin.layout')

@section('title', $banner->exists ? 'ویرایش اسلاید' : 'اسلاید جدید')

@section('content')
    <form method="POST" enctype="multipart/form-data" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" class="card p-5 max-w-3xl grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($banner->exists) @method('PUT') @endif
        <div class="sm:col-span-2">
            <label class="form-label" for="title">عنوان *</label>
            <input id="title" name="title" value="{{ old('title', $banner->title) }}" required maxlength="150" class="form-input">
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="subtitle">زیرعنوان</label>
            <textarea id="subtitle" name="subtitle" rows="2" maxlength="300" class="form-input">{{ old('subtitle', $banner->subtitle) }}</textarea>
        </div>
        <div>
            <label class="form-label" for="badge">برچسب کوچک بالای عنوان</label>
            <input id="badge" name="badge" value="{{ old('badge', $banner->badge) }}" maxlength="60" class="form-input" placeholder="واردات مستقیم از مسقط">
        </div>
        <div>
            <label class="form-label" for="sort_order">ترتیب</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-input">
        </div>
        <div>
            <label class="form-label" for="button_text">متن دکمه</label>
            <input id="button_text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" maxlength="40" class="form-input" placeholder="مشاهده قطعات">
        </div>
        <div>
            <label class="form-label" for="button_url">لینک دکمه</label>
            <input id="button_url" name="button_url" value="{{ old('button_url', $banner->button_url) }}" maxlength="255" dir="ltr" class="form-input text-left" placeholder="/products?brand=mercedes-benz">
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="image">تصویر پس‌زمینه</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-preview="banner-preview" class="form-input">
            <div id="banner-preview" class="flex gap-2 mt-2"></div>
            @if ($banner->imageUrl())
                <div class="flex items-center gap-3 mt-2">
                    <img src="{{ $banner->imageUrl() }}" alt="" class="h-16 rounded-lg">
                    <label class="text-[11px] text-rose-300 flex items-center gap-1"><input type="checkbox" name="remove_image" value="1" class="form-check"> حذف تصویر</label>
                </div>
            @endif
        </div>
        <label class="flex items-center gap-2 text-xs text-slate-300"><input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $banner->is_active))> فعال</label>
        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
            <a href="{{ route('admin.banners.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
