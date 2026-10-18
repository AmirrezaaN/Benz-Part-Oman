@extends('admin.layout')

@section('title', 'سفارش '.$order->code)

@section('content')
    <div class="grid xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 space-y-5 min-w-0">
            <div class="card overflow-x-auto">
                <div class="flex items-center justify-between gap-3 px-4 py-3.5">
                    <h2 class="text-sm font-bold text-white">اقلام سفارش <span class="font-mono text-slate-400">{{ $order->code }}</span></h2>
                    <x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" />
                </div>
                <table class="table-base">
                    <thead><tr><th>قطعه</th><th>تعداد</th><th>موجودی فعلی</th><th>واحد (OMR)</th><th>واحد (تومان)</th><th>جمع</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="max-w-64">
                                    @if ($item->product && ! $item->product->trashed())
                                        <a href="{{ route('admin.products.edit', $item->product) }}" class="text-white hover:text-sky-400">{{ $item->product_name }}</a>
                                    @else
                                        {{ $item->product_name }} <span class="text-rose-400 text-[10px]">(حذف شده)</span>
                                    @endif
                                    @if ($item->part_number)<div class="text-[10px] text-slate-500 font-mono" dir="ltr">{{ $item->part_number }}</div>@endif
                                </td>
                                <td class="font-bold text-white">{{ fa_num($item->quantity) }}</td>
                                <td>{{ $item->product?->quantity === null ? '—' : fa_num($item->product->quantity) }}</td>
                                <td dir="ltr" class="text-right">{{ format_omr($item->unit_price_omr) }}</td>
                                <td>{{ format_toman($item->unit_price_toman) }}</td>
                                <td class="font-bold text-white whitespace-nowrap">{{ format_toman($item->unit_price_toman * $item->quantity) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-slate-400">نرخ ثبت سفارش: {{ format_toman($order->exchange_rate) }} تومان</td>
                            <td dir="ltr" class="text-right font-bold">{{ format_omr($order->subtotal_omr) }}</td>
                            <td></td>
                            <td class="font-black text-sky-400 whitespace-nowrap">{{ format_toman($order->total_toman) }} ت</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="card p-5 space-y-4">
                @csrf @method('PUT')
                <h2 class="text-sm font-bold text-white"><i class="fa-solid fa-arrows-rotate text-sky-400 ml-1.5"></i>تغییر وضعیت</h2>
                <p class="text-[11px] text-slate-400 leading-6">
                    بعد از استعلام، با رفتن به وضعیت «موجودی تایید شد» یا بعد از آن، موجودی محصولات کم می‌شود؛ با «لغو» موجودی برگردانده می‌شود.
                    @if ($order->stock_deducted)<span class="text-emerald-400">(موجودی این سفارش کسر شده است)</span>@endif
                </p>
                <fieldset @cannot('orders.manage') disabled @endcannot class="grid sm:grid-cols-3 gap-3">
                    <select name="status" class="form-input">
                        @foreach (config('shop.order_statuses') as $key => $s)
                            <option value="{{ $key }}" @selected(old('status', $order->status) === $key)>{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                    <textarea name="admin_note" rows="2" maxlength="3000" class="form-input sm:col-span-2" placeholder="پیام برای مشتری (در صفحه سفارش او نمایش داده می‌شود)">{{ old('admin_note', $order->admin_note) }}</textarea>
                    <button class="btn-primary sm:col-span-3 w-fit"><i class="fa-solid fa-floppy-disk"></i> ذخیره</button>
                </fieldset>
            </form>
        </div>

        <aside class="space-y-5">
            <div class="card p-5 text-xs space-y-2.5">
                <h2 class="text-sm font-bold text-white mb-1">اطلاعات ارسال</h2>
                <p><span class="text-slate-500">گیرنده:</span> {{ $order->full_name }}</p>
                <p><span class="text-slate-500">موبایل:</span> <a href="tel:{{ $order->phone }}" dir="ltr">{{ $order->phone }}</a></p>
                <p><span class="text-slate-500">آدرس:</span> {{ $order->province }}، {{ $order->city }}، {{ $order->address }}</p>
                @if ($order->postal_code)<p><span class="text-slate-500">کد پستی:</span> <span class="select-all">{{ $order->postal_code }}</span></p>@endif
                @if ($order->customer_note)<p class="bg-slate-950/50 rounded-lg p-2.5 leading-6"><span class="text-slate-500">توضیح مشتری:</span> {{ $order->customer_note }}</p>@endif
                <p class="text-[10px] text-slate-500 pt-2">ثبت: {{ jdate($order->created_at, 'Y/m/d H:i') }}</p>
            </div>
            <div class="card p-5 text-xs space-y-2.5">
                <h2 class="text-sm font-bold text-white mb-1">حساب مشتری</h2>
                <p class="text-white font-bold">{{ $order->user?->full_name }}</p>
                @if ($order->user?->email)<p dir="ltr" class="text-right">{{ $order->user->email }}</p>@endif
                @can('users.view')
                    @if ($order->user)<a href="{{ route('admin.users.show', $order->user) }}" class="btn-ghost btn-sm w-full">پروفایل مشتری</a>@endif
                @endcan
                <a href="{{ whatsapp_link(null, $order->phone) }}" target="_blank" rel="noopener" class="btn-success btn-sm w-full"><i class="fa-brands fa-whatsapp"></i> پیام به گیرنده</a>
            </div>
        </aside>
    </div>
@endsection
