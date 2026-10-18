@extends('admin.layout')

@section('title', $category->exists ? 'ویرایش بخش' : 'بخش جدید')

@section('content')
    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="card p-5 max-w-2xl grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($category->exists) @method('PUT') @endif
        <div>
            <label class="form-label" for="name">نام بخش *</label>
            <input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100" class="form-input">
        </div>
        <div>
            <label class="form-label" for="parent_id">زیرمجموعه‌ی (اختیاری)</label>
            <select id="parent_id" name="parent_id" class="form-input">
                <option value="">— بخش اصلی —</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="icon">آیکون (Font Awesome)</label>
            <input id="icon" name="icon" value="{{ old('icon', $category->icon) }}" dir="ltr" maxlength="60" class="form-input text-left font-mono" placeholder="fa-gears">
            <p class="form-hint">نمونه‌ها: fa-gears، fa-car-side، fa-lightbulb، fa-microchip، fa-filter</p>
        </div>
        <div>
            <label class="form-label" for="sort_order">ترتیب نمایش</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-input">
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="description">توضیحات</label>
            <textarea id="description" name="description" rows="2" maxlength="500" class="form-input">{{ old('description', $category->description) }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-xs text-slate-300"><input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $category->is_active))> فعال</label>
        <label class="flex items-center gap-2 text-xs text-slate-300"><input type="checkbox" name="show_on_home" value="1" class="form-check" @checked(old('show_on_home', $category->show_on_home))> نمایش اسلایدر این بخش در صفحه اصلی</label>
        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
