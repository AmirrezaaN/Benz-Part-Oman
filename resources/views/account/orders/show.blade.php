@extends('account.layout')

@section('title', 'سفارش '.$order->code)

@section('account')
    @php
        $flow = ['pending', 'confirmed', 'paid', 'shipping', 'delivered'];
        $current = array_search($order->status, $flow, true);
    @endphp
    <div class="card p-5">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-base font-black text-white">سفارش <span class="font-mono">{{ $order->code }}</span></h1>
                <p class="text-[11px] text-slate-500 mt-1">ثبت شده در {{ jdate($order->created_at, 'j F Y - H:i') }}</p>
            </div>
            <x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" class="text-xs!" />
        </div>

        @if ($order->status !== 'cancelled')
            <ol class="grid grid-cols-5 gap-1 mt-6 text-[10px] text-center">
                @foreach ($flow as $i => $status)
                    <li>
                        <div class="h-1.5 rounded-full {{ $current !== false && $i <= $current ? 'bg-sky-500' : 'bg-slate-800' }}"></div>
                        <div class="mt-2 {{ $current !== false && $i <= $current ? 'text-sky-300' : 'text-slate-600' }}">{{ config("shop.order_statuses.$status.short") }}</div>
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($order->status === 'pending')
            <div class="mt-5 bg-amber-500/5 border border-amber-500/20 rounded-xl p-4 text-xs text-amber-100 leading-7 flex gap-3">
                <i class="fa-solid fa-magnifying-glass-chart text-amber-300 text-lg mt-1"></i>
                <div>
                    <strong class="text-amber-300">در حال استعلام موجودی از انبار عمان هستیم.</strong><br>
                    کارشناسان ما موجودی و سلامت قطعات این سفارش را در انبار مسقط/دبی بررسی می‌کنند و قیمت نهایی و هزینه ارسال را اعلام می‌کنند.
                    پس از تایید موجودی، برای پرداخت با شما تماس می‌گیریم و نتیجه در همین صفحه هم نمایش داده می‌شود. تا آن زمان هیچ پرداختی لازم نیست.
                </div>
            </div>
        @elseif ($order->status === 'confirmed')
            <div class="mt-5 bg-sky-500/5 border border-sky-500/20 rounded-xl p-4 text-xs text-sky-100 leading-7 flex gap-3">
                <i class="fa-solid fa-circle-check text-sky-300 text-lg mt-1"></i>
                <div><strong class="text-sky-300">موجودی قطعات تایید شد.</strong> کارشناس ما برای هماهنگی پرداخت با شما تماس می‌گیرد.</div>
            </div>
        @endif
    </div>

    <div class="card mt-5 overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>قطعه</th><th>تعداد</th><th>قیمت واحد</th><th>جمع</th></tr></thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            @if ($item->product && ! $item->product->trashed())
                                <a href="{{ route('products.show', $item->product) }}" class="text-white hover:text-sky-400">{{ $item->product_name }}</a>
                            @else
                                {{ $item->product_name }}
                            @endif
                            @if ($item->part_number)<div class="text-[10px] text-slate-500 font-mono" dir="ltr">{{ $item->part_number }}</div>@endif
                        </td>
                        <td>{{ fa_num($item->quantity) }}</td>
                        <td class="whitespace-nowrap">{{ format_toman($item->unit_price_toman) }}</td>
                        <td class="whitespace-nowrap font-bold text-white">{{ format_toman($item->unit_price_toman * $item->quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="font-bold text-white">جمع کل</td><td class="font-black text-sky-400 whitespace-nowrap">{{ format_toman($order->total_toman) }} تومان</td></tr>
            </tfoot>
        </table>
    </div>

    <div class="grid md:grid-cols-2 gap-5 mt-5">
        <div class="card p-5 text-xs space-y-2">
            <h2 class="text-sm font-bold text-white mb-3">اطلاعات ارسال</h2>
            <p><span class="text-slate-500">گیرنده:</span> {{ $order->full_name }} · <span dir="ltr">{{ $order->phone }}</span></p>
            <p><span class="text-slate-500">آدرس:</span> {{ $order->province }}، {{ $order->city }}، {{ $order->address }}</p>
            @if ($order->postal_code)<p><span class="text-slate-500">کد پستی:</span> {{ $order->postal_code }}</p>@endif
            @if ($order->customer_note)<p><span class="text-slate-500">توضیحات:</span> {{ $order->customer_note }}</p>@endif
        </div>
        <div class="card p-5 text-xs">
            <h2 class="text-sm font-bold text-white mb-3">پیام فروشگاه</h2>
            <p class="text-slate-300 leading-7 whitespace-pre-line">{{ $order->admin_note ?: 'هنوز پیامی ثبت نشده است. نتیجه استعلام موجودی به‌زودی اینجا اعلام می‌شود.' }}</p>
            <a href="{{ whatsapp_link('سلام، پیگیری سفارش '.$order->code) }}" target="_blank" rel="noopener" class="btn-success btn-sm mt-4"><i class="fa-brands fa-whatsapp"></i> پیگیری در واتساپ</a>
        </div>
    </div>
@endsection
