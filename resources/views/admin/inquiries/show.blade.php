@extends('admin.layout')

@section('title', 'استعلام '.$inquiry->code)

@section('content')
    <div class="grid xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 space-y-5 min-w-0">
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h2 class="text-base font-black text-white">{{ $inquiry->part_name }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1 font-mono">{{ $inquiry->code }} · {{ jdate($inquiry->created_at, 'Y/m/d H:i') }}</p>
                    </div>
                    <x-status-badge :color="$inquiry->statusColor()" :label="$inquiry->statusLabel()" class="text-xs!" />
                </div>
                <div class="grid sm:grid-cols-2 gap-x-6 gap-y-2.5 text-xs">
                    @foreach ([
                        'شماره شاسی (VIN)' => $inquiry->vin, 'پارت نامبر' => $inquiry->part_number, 'برند' => $inquiry->brand?->name,
                        'مدل' => $inquiry->car_model, 'سال' => $inquiry->year, 'تعداد' => $inquiry->quantity,
                    ] as $label => $value)
                        <div class="flex justify-between border-b border-slate-800/70 pb-2">
                            <span class="text-slate-500">{{ $label }}</span>
                            <span class="text-white font-mono select-all" dir="ltr">{{ $value ?: '—' }}</span>
                        </div>
                    @endforeach
                </div>
                @if ($inquiry->product)
                    <p class="text-xs mt-4">محصول مرتبط: <a href="{{ route('products.show', $inquiry->product) }}" target="_blank" class="text-sky-400">{{ $inquiry->product->name }}</a></p>
                @endif
                @if ($inquiry->message)
                    <p class="text-xs text-slate-300 mt-4 leading-7 whitespace-pre-line bg-slate-950/50 rounded-xl p-3.5">{{ $inquiry->message }}</p>
                @endif
                @if ($inquiry->imageUrl())
                    <a href="{{ $inquiry->imageUrl() }}" target="_blank"><img src="{{ $inquiry->imageUrl() }}" alt="" class="mt-4 w-48 h-48 object-cover rounded-xl border border-slate-800"></a>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}" class="card p-5 space-y-4">
                @csrf @method('PUT')
                <h2 class="text-sm font-bold text-white"><i class="fa-solid fa-reply text-emerald-400 ml-1.5"></i>پاسخ به مشتری</h2>
                @cannot('inquiries.reply')
                    <p class="text-xs text-amber-300">شما دسترسی پاسخ به استعلام را ندارید.</p>
                @endcannot
                <fieldset @cannot('inquiries.reply') disabled @endcannot class="space-y-4">
                    <textarea name="admin_reply" rows="5" maxlength="5000" class="form-input" placeholder="متن پاسخ که در حساب مشتری نمایش داده می‌شود...">{{ old('admin_reply', $inquiry->admin_reply) }}</textarea>
                    <div class="grid sm:grid-cols-3 gap-3">
                        <div>
                            <label class="form-label" for="quoted_price_omr">قیمت پیشنهادی (OMR)</label>
                            <input id="quoted_price_omr" name="quoted_price_omr" value="{{ old('quoted_price_omr', $inquiry->quoted_price_omr !== null ? format_omr($inquiry->quoted_price_omr) : '') }}" dir="ltr" data-latin-digits
                                   data-omr-input="q-toman" data-rate="{{ exchange_rate() }}" class="form-input text-left">
                            <p id="q-toman" class="text-[11px] text-emerald-400 mt-1"></p>
                        </div>
                        <div>
                            <label class="form-label" for="quoted_price_toman">قیمت پیشنهادی (تومان)</label>
                            <input id="quoted_price_toman" name="quoted_price_toman" value="{{ old('quoted_price_toman', $inquiry->quoted_price_toman) }}" dir="ltr" data-latin-digits class="form-input text-left">
                        </div>
                        <div>
                            <label class="form-label" for="status">وضعیت</label>
                            <select id="status" name="status" class="form-input">
                                @foreach (config('shop.inquiry_statuses') as $key => $s)
                                    <option value="{{ $key }}" @selected(old('status', $inquiry->admin_reply ? $inquiry->status : ($inquiry->status === 'pending' ? 'answered' : $inquiry->status)) === $key)>{{ $s['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn-success"><i class="fa-solid fa-paper-plane"></i> ثبت پاسخ / وضعیت</button>
                </fieldset>
                @if ($inquiry->replied_at)
                    <p class="text-[10px] text-slate-500">آخرین پاسخ: {{ $inquiry->repliedBy?->full_name }} · {{ jdate($inquiry->replied_at, 'Y/m/d H:i') }}</p>
                @endif
            </form>
        </div>

        <aside class="card p-5 h-fit text-xs space-y-3">
            <h2 class="text-sm font-bold text-white">مشتری</h2>
            <p class="text-white font-bold">{{ $inquiry->user?->full_name }}</p>
            @if ($inquiry->user?->phone)
                <p><i class="fa-solid fa-mobile-screen text-slate-500 ml-1.5"></i><a href="tel:{{ $inquiry->user->phone }}" dir="ltr">{{ $inquiry->user->phone }}</a></p>
            @endif
            @if ($inquiry->user?->email)
                <p><i class="fa-solid fa-envelope text-slate-500 ml-1.5"></i><span dir="ltr">{{ $inquiry->user->email }}</span></p>
            @endif
            @can('users.view')
                @if ($inquiry->user)<a href="{{ route('admin.users.show', $inquiry->user) }}" class="btn-ghost btn-sm w-full">پروفایل مشتری</a>@endif
            @endcan
            @if ($inquiry->user?->phone)
                <a href="{{ whatsapp_link(null, $inquiry->user->phone) }}" target="_blank" rel="noopener" class="btn-success btn-sm w-full"><i class="fa-brands fa-whatsapp"></i> پیام در واتساپ</a>
            @endif
        </aside>
    </div>
@endsection
