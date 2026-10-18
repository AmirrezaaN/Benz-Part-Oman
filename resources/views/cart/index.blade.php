@extends('layouts.app')

@section('title', 'سبد خرید')

@section('content')
    <div class="container-x py-8">
        <h1 class="text-lg font-black text-white mb-6"><i class="fa-solid fa-cart-shopping text-sky-400 ml-2"></i>سبد خرید</h1>

        @if ($lines->isEmpty())
            <div class="card text-center py-16">
                <i class="fa-solid fa-cart-arrow-down text-5xl text-slate-700"></i>
                <h2 class="text-sm font-bold text-slate-300 mt-4">سبد خرید شما خالی است</h2>
                <p class="text-xs text-slate-500 mt-2">از بین هزاران قطعه اصلی، قطعه مورد نیاز خودروی خود را پیدا کنید.</p>
                <a href="{{ route('products.index') }}" class="btn-primary mt-6"><i class="fa-solid fa-border-all"></i> مشاهده قطعات</a>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-3">
                    @foreach ($lines as $line)
                        @php($p = $line['product'])
                        <div class="card p-3 sm:p-4 flex gap-4">
                            <a href="{{ route('products.show', $p) }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden shrink-0 bg-slate-950 border border-slate-800">
                                <x-product-image :product="$p" />
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $p) }}" class="text-xs sm:text-sm font-bold text-white hover:text-sky-400 line-clamp-2 leading-6">{{ $p->name }}</a>
                                @if ($p->part_number)
                                    <div class="text-[10px] text-slate-500 font-mono mt-1" dir="ltr">{{ $p->part_number }}</div>
                                @endif
                                <div class="flex items-center justify-between gap-3 mt-3 flex-wrap">
                                    <form method="POST" action="{{ route('cart.update', $p) }}" class="flex items-center gap-1.5">
                                        @csrf @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="0" max="{{ $p->quantity }}" data-autosubmit
                                               class="form-input w-16 py-1.5! text-center text-xs" aria-label="تعداد">
                                        <span class="text-[10px] text-slate-500">از {{ fa_num($p->quantity) }} موجود</span>
                                    </form>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-black text-white">
                                            <span class="price-toman">{{ format_toman($line['toman']) }} <small class="text-[10px] text-slate-400 font-normal">تومان</small></span>
                                            <span class="price-omr" dir="ltr">{{ format_omr($line['omr']) }} <small class="text-[10px] text-slate-400 font-normal">OMR</small></span>
                                        </span>
                                        <form method="POST" action="{{ route('cart.remove', $p) }}">
                                            @csrf @method('DELETE')
                                            <button class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600 text-slate-400 hover:text-white transition" aria-label="حذف"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="card p-5 h-fit lg:sticky lg:top-40">
                    <h2 class="text-sm font-bold text-white mb-4">خلاصه سفارش</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-400"><span>تعداد اقلام</span><span>{{ fa_num($lines->sum('quantity')) }} عدد</span></div>
                        <div class="flex justify-between text-slate-400"><span>جمع به ریال عمان</span><span dir="ltr">{{ format_omr($totalOmr) }} OMR</span></div>
                        <div class="flex justify-between text-slate-400"><span>هزینه ارسال</span><span>پس از تایید سفارش اعلام می‌شود</span></div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-800">
                            <span class="font-bold text-white">مبلغ قابل پرداخت</span>
                            <span class="text-lg font-black text-sky-400">{{ format_toman($totalToman) }} <small class="text-[10px] text-slate-400 font-normal">تومان</small></span>
                        </div>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn-gradient w-full mt-5 py-3!"><i class="fa-solid fa-magnifying-glass-chart"></i> ادامه و استعلام موجودی</a>
                    <p class="text-[10px] text-slate-500 mt-3 leading-5">قیمت تومانی بر اساس نرخ روز ریال عمان ({{ format_toman(exchange_rate()) }} تومان) محاسبه شده است.</p>
                </aside>
            </div>
        @endif
    </div>
@endsection
