@extends('admin.layout')

@section('title', $model->exists ? 'ویرایش مدل خودرو' : 'مدل خودرو جدید')

@section('content')
    <form method="POST" action="{{ $model->exists ? route('admin.car-models.update', $model) : route('admin.car-models.store') }}" class="card p-5 max-w-2xl grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($model->exists) @method('PUT') @endif
        <div>
            <label class="form-label" for="brand_id">برند *</label>
            <select id="brand_id" name="brand_id" required class="form-input">
                <option value="">انتخاب کنید</option>
                @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id', $model->brand_id) == $b->id)>{{ $b->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="name">نام مدل *</label>
            <input id="name" name="name" value="{{ old('name', $model->name) }}" required maxlength="80" dir="ltr" class="form-input text-left" placeholder="E-Class">
        </div>
        <div>
            <label class="form-label" for="name_fa">نام فارسی</label>
            <input id="name_fa" name="name_fa" value="{{ old('name_fa', $model->name_fa) }}" maxlength="80" class="form-input" placeholder="کلاس E">
        </div>
        <div>
            <label class="form-label" for="sort_order">ترتیب</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $model->sort_order ?? 0) }}" class="form-input">
        </div>
        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
            <a href="{{ route('admin.car-models.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
