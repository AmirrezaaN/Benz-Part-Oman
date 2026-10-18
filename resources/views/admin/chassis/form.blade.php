@extends('admin.layout')

@section('title', $item->exists ? 'ویرایش شاسی' : 'شاسی جدید')

@section('content')
    <form method="POST" action="{{ $item->exists ? route('admin.chassis.update', $item) : route('admin.chassis.store') }}" class="card p-5 max-w-2xl grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div>
            <label class="form-label" for="car_model_id">مدل خودرو *</label>
            <select id="car_model_id" name="car_model_id" required class="form-input">
                <option value="">انتخاب کنید</option>
                @foreach ($brands as $b)
                    <optgroup label="{{ $b->name }}">
                        @foreach ($b->carModels as $m)<option value="{{ $m->id }}" @selected(old('car_model_id', $item->car_model_id) == $m->id)>{{ $m->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="code">کد شاسی *</label>
            <input id="code" name="code" value="{{ old('code', $item->code) }}" required maxlength="30" dir="ltr" class="form-input text-left font-mono uppercase" placeholder="W213">
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="title">عنوان (اختیاری)</label>
            <input id="title" name="title" value="{{ old('title', $item->title) }}" maxlength="100" class="form-input" placeholder="مثلاً: سدان / کوپه / فیس‌لیفت">
        </div>
        <div>
            <label class="form-label" for="year_from">از سال</label>
            <input id="year_from" name="year_from" value="{{ old('year_from', $item->year_from) }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input">
        </div>
        <div>
            <label class="form-label" for="year_to">تا سال (خالی = هنوز تولید می‌شود)</label>
            <input id="year_to" name="year_to" value="{{ old('year_to', $item->year_to) }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input">
        </div>
        <div>
            <label class="form-label" for="sort_order">ترتیب</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="form-input">
        </div>
        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
            <a href="{{ route('admin.chassis.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
