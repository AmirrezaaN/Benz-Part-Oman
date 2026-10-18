@extends('account.layout')

@section('title', 'استعلام '.$inquiry->code)

@section('account')
    <div class="card p-5">
        <div class="flex items-start justify-between gap-3 flex-wrap mb-5">
            <div>
                <h1 class="text-base font-black text-white">{{ $inquiry->part_name }}</h1>
                <p class="text-[11px] text-slate-500 mt-1 font-mono">{{ $inquiry->code }} · {{ jdate($inquiry->created_at, 'Y/m/d H:i') }}</p>
            </div>
            <x-status-badge :color="$inquiry->statusColor()" :label="$inquiry->statusLabel()" class="text-xs!" />
        </div>

        <div class="grid sm:grid-cols-2 gap-x-6 gap-y-2.5 text-xs">
            @foreach (array_filter([
                'شماره شاسی (VIN)' => $inquiry->vin,
                'پارت نامبر' => $inquiry->part_number,
                'برند' => $inquiry->brand?->name,
                'مدل' => $inquiry->car_model,
                'سال' => $inquiry->year,
                'تعداد' => fa_num($inquiry->quantity),
            ]) as $label => $value)
                <div class="flex justify-between border-b border-slate-800/70 pb-2">
                    <span class="text-slate-500">{{ $label }}</span>
                    <span class="text-white font-mono" dir="ltr">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        @if ($inquiry->product)
            <a href="{{ route('products.show', $inquiry->product) }}" class="mt-4 inline-flex items-center gap-2 text-xs text-sky-400"><i class="fa-solid fa-link"></i> {{ $inquiry->product->name }}</a>
        @endif
        @if ($inquiry->message)
            <p class="text-xs text-slate-300 mt-4 leading-7 whitespace-pre-line bg-slate-950/50 rounded-xl p-3.5">{{ $inquiry->message }}</p>
        @endif
        @if ($inquiry->imageUrl())
            <img src="{{ $inquiry->imageUrl() }}" alt="" class="mt-4 w-40 h-40 object-cover rounded-xl border border-slate-800">
        @endif
    </div>

    <div class="card p-5 mt-5 {{ $inquiry->admin_reply ? 'border-emerald-500/30!' : '' }}">
        <h2 class="text-sm font-bold text-white mb-3"><i class="fa-solid fa-reply text-emerald-400 ml-1.5"></i>پاسخ کارشناس</h2>
        @if ($inquiry->admin_reply)
            <p class="text-sm text-slate-200 leading-8 whitespace-pre-line">{{ $inquiry->admin_reply }}</p>
            @if ($inquiry->quoted_price_toman || $inquiry->quoted_price_omr)
                <div class="flex items-center gap-4 mt-4 flex-wrap">
                    @if ($inquiry->quoted_price_toman)
                        <span class="badge bg-sky-500/10 text-sky-300 border-sky-500/30 text-sm! px-3! py-1.5!">{{ format_toman($inquiry->quoted_price_toman) }} تومان</span>
                    @endif
                    @if ($inquiry->quoted_price_omr)
                        <span class="badge bg-slate-800 text-slate-300 border-slate-700 text-sm! px-3! py-1.5!" dir="ltr">{{ format_omr($inquiry->quoted_price_omr) }} OMR</span>
                    @endif
                </div>
            @endif
            <p class="text-[10px] text-slate-500 mt-4">{{ jdate($inquiry->replied_at, 'Y/m/d H:i') }}</p>
            <a href="{{ whatsapp_link('سلام، درباره استعلام '.$inquiry->code.' سوال داشتم.') }}" target="_blank" rel="noopener" class="btn-success btn-sm mt-4"><i class="fa-brands fa-whatsapp"></i> پیگیری و سفارش در واتساپ</a>
        @else
            <p class="text-xs text-slate-400"><i class="fa-regular fa-clock ml-1"></i> استعلام شما در صف بررسی است. پاسخ کارشناسان به‌زودی در همین صفحه نمایش داده می‌شود.</p>
        @endif
    </div>
@endsection
