@extends('layouts.app')

@section('title', 'استعلام قطعه')

@section('content')
    @php($q = fn ($key, $default = null) => old($key, request()->query($key, $default)))
    <div class="container-x py-8 max-w-4xl">
        <div class="mb-6">
            <h1 class="text-lg font-black text-white"><i class="fa-solid fa-magnifying-glass-plus text-amber-300 ml-2"></i>استعلام قیمت و موجودی قطعه</h1>
            <p class="text-xs text-slate-400 mt-2 leading-6">هرچه اطلاعات دقیق‌تری وارد کنید (به‌خصوص شماره شاسی VIN)، پاسخ سریع‌تر و دقیق‌تر خواهد بود. پاسخ در بخش «استعلام‌های من» نمایش داده می‌شود.</p>
        </div>

        @if ($product)
            <div class="card p-3 flex items-center gap-3 mb-5">
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-950 shrink-0"><x-product-image :product="$product" /></div>
                <div class="min-w-0">
                    <div class="text-[10px] text-slate-500">استعلام برای قطعه:</div>
                    <a href="{{ route('products.show', $product) }}" class="text-xs font-bold text-white hover:text-sky-400 truncate block">{{ $product->name }}</a>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('inquiries.store') }}" enctype="multipart/form-data" class="card p-5 sm:p-6 grid sm:grid-cols-2 gap-4">
            @csrf
            @if ($product)
                <input type="hidden" name="product_id" value="{{ $product->id }}">
            @endif

            <div class="sm:col-span-2">
                <label class="form-label" for="part_name">نام قطعه <span class="text-rose-400">*</span></label>
                <input id="part_name" name="part_name" value="{{ $q('part_name', $product?->name) }}" required maxlength="150" class="form-input" placeholder="مثلاً: چراغ جلو سمت چپ">
                @error('part_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="vin">شماره شاسی (VIN) — ۱۷ کاراکتر</label>
                <input id="vin" name="vin" value="{{ $q('vin') }}" maxlength="17" dir="ltr" data-latin-digits class="form-input font-mono uppercase text-left" placeholder="WDD2130421A123456">
                @error('vin')<p class="form-error">{{ $message }}</p>@enderror
                <p class="form-hint">روی کارت ماشین یا پایین شیشه جلو درج شده است.</p>
            </div>
            <div>
                <label class="form-label" for="part_number">پارت نامبر</label>
                <input id="part_number" name="part_number" value="{{ $q('part_number', $product?->part_number) }}" maxlength="60" dir="ltr" data-latin-digits class="form-input font-mono text-left" placeholder="A2223204713">
                @error('part_number')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="brand_id">برند خودرو</label>
                <select id="brand_id" name="brand_id" class="form-input">
                    <option value="">انتخاب کنید</option>
                    @foreach ($brands as $b)
                        <option value="{{ $b->id }}" @selected((string) $q('brand_id', $product?->brand_id) === (string) $b->id)>{{ $b->name }}{{ $b->name_fa ? ' - '.$b->name_fa : '' }}</option>
                    @endforeach
                </select>
                @error('brand_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="form-label" for="car_model">مدل / تیپ</label>
                    <input id="car_model" name="car_model" value="{{ $q('car_model') }}" maxlength="100" class="form-input" placeholder="E250 AMG">
                </div>
                <div>
                    <label class="form-label" for="year">سال</label>
                    <input id="year" name="year" value="{{ $q('year') }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input" placeholder="2018">
                </div>
                @error('year')<p class="form-error col-span-3">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="quantity">تعداد مورد نیاز</label>
                <input id="quantity" name="quantity" type="number" min="1" max="999" value="{{ $q('quantity', 1) }}" class="form-input">
                @error('quantity')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="image">عکس قطعه (اختیاری)</label>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-preview="inq-preview"
                       class="form-input file:ml-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:px-3 file:py-1 file:text-xs">
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
                <div id="inq-preview" class="flex gap-2 mt-2"></div>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="message">توضیحات تکمیلی</label>
                <textarea id="message" name="message" rows="4" maxlength="2000" class="form-input" placeholder="تیپ موتور، سمت قطعه (چپ/راست)، نو یا استوک بودن و ...">{{ $q('message') }}</textarea>
                @error('message')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="btn-gradient px-8 py-3!"><i class="fa-solid fa-paper-plane"></i> ارسال استعلام</button>
            </div>
        </form>
    </div>
@endsection
