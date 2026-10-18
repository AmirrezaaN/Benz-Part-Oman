@extends('admin.layout')

@section('title', $brand->exists ? 'ویرایش برند' : 'برند جدید')

@section('content')
    <form method="POST" enctype="multipart/form-data" action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" class="card p-5 max-w-2xl grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($brand->exists) @method('PUT') @endif
        <div>
            <label class="form-label" for="name">نام انگلیسی *</label>
            <input id="name" name="name" value="{{ old('name', $brand->name) }}" required maxlength="80" dir="ltr" class="form-input text-left" placeholder="Mercedes-Benz">
        </div>
        <div>
            <label class="form-label" for="name_fa">نام فارسی</label>
            <input id="name_fa" name="name_fa" value="{{ old('name_fa', $brand->name_fa) }}" maxlength="80" class="form-input" placeholder="مرسدس بنز">
        </div>
        <div>
            <label class="form-label" for="sort_order">ترتیب</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $brand->sort_order ?? 0) }}" class="form-input">
        </div>
        <div>
            <label class="form-label" for="logo">لوگو (PNG شفاف پیشنهاد می‌شود)</label>
            <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="form-input">
            @if ($brand->logoUrl())
                <div class="flex items-center gap-3 mt-2">
                    <img src="{{ $brand->logoUrl() }}" alt="" class="w-10 h-10 object-contain bg-slate-950 rounded-lg p-1">
                    <label class="text-[11px] text-rose-300 flex items-center gap-1"><input type="checkbox" name="remove_logo" value="1" class="form-check"> حذف لوگو</label>
                </div>
            @endif
        </div>
        <label class="flex items-center gap-2 text-xs text-slate-300"><input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $brand->is_active))> فعال</label>
        <label class="flex items-center gap-2 text-xs text-slate-300"><input type="checkbox" name="is_featured" value="1" class="form-check" @checked(old('is_featured', $brand->is_featured))> برند ویژه</label>
        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
            <a href="{{ route('admin.brands.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
