@extends('account.layout')

@section('title', 'سفارش‌های من')

@section('account')
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-800"><h1 class="text-sm font-bold text-white">سفارش‌های من</h1></div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>کد سفارش</th><th>تاریخ</th><th>اقلام</th><th>مبلغ</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="font-mono">{{ $order->code }}</td>
                            <td class="whitespace-nowrap">{{ jdate($order->created_at) }}</td>
                            <td>{{ fa_num($order->items_count) }}</td>
                            <td class="whitespace-nowrap">{{ format_toman($order->total_toman) }} تومان</td>
                            <td><x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" /></td>
                            <td><a href="{{ route('account.orders.show', $order) }}" class="text-sky-400 font-bold">جزئیات</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-10 text-slate-500">هنوز سفارشی ثبت نکرده‌اید.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $orders->links() }}
@endsection
