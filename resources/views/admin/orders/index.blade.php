@extends('admin.layout')

@section('title', 'سفارش‌ها')

@section('content')
    @include('admin.partials.status-tabs', ['statuses' => config('shop.order_statuses'), 'counts' => $counts, 'route' => 'admin.orders.index'])

    <form method="GET" data-clean class="flex gap-2 mb-4">
        @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input name="q" value="{{ request('q') }}" placeholder="کد سفارش، نام یا موبایل گیرنده..." class="form-input py-2! text-xs max-w-72">
        <button class="btn-ghost btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>کد</th><th>مشتری</th><th>گیرنده</th><th>اقلام</th><th>مبلغ</th><th>تاریخ</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="font-mono whitespace-nowrap">{{ $order->code }}</td>
                        <td class="whitespace-nowrap">{{ $order->user?->full_name }}</td>
                        <td class="whitespace-nowrap">{{ $order->full_name }}<div class="text-[10px] text-slate-500" dir="ltr">{{ $order->phone }}</div></td>
                        <td>{{ fa_num($order->items_count) }}</td>
                        <td class="whitespace-nowrap">{{ format_toman($order->total_toman) }} ت<div class="text-[10px] text-slate-500" dir="ltr">{{ format_omr($order->subtotal_omr) }} OMR</div></td>
                        <td class="whitespace-nowrap">{{ jdate($order->created_at, 'Y/m/d H:i') }}</td>
                        <td><x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" /></td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-primary btn-sm">جزئیات</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-10 text-slate-500">سفارشی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
@endsection
