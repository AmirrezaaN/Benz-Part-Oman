@extends('admin.layout')

@section('title', 'استعلام‌ها')

@section('content')
    @include('admin.partials.status-tabs', ['statuses' => config('shop.inquiry_statuses'), 'counts' => $counts, 'route' => 'admin.inquiries.index'])

    <form method="GET" data-clean class="flex gap-2 mb-4">
        @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input name="q" value="{{ request('q') }}" placeholder="کد، نام قطعه، VIN، پارت نامبر..." class="form-input py-2! text-xs max-w-72">
        <button class="btn-ghost btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>کد</th><th>قطعه</th><th>VIN / پارت نامبر</th><th>مشتری</th><th>تاریخ</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
                @forelse ($inquiries as $inq)
                    <tr>
                        <td class="font-mono whitespace-nowrap">{{ $inq->code }}</td>
                        <td class="max-w-56">
                            <div class="font-bold text-white truncate">{{ $inq->part_name }}</div>
                            @if ($inq->product)<div class="text-[10px] text-sky-400 truncate">{{ $inq->product->name }}</div>@endif
                        </td>
                        <td class="font-mono text-[11px]" dir="ltr">{{ $inq->vin ?: '—' }}<br><span class="text-slate-500">{{ $inq->part_number }}</span></td>
                        <td class="whitespace-nowrap">
                            @if ($inq->user)
                                @can('users.view')<a href="{{ route('admin.users.show', $inq->user) }}" class="hover:text-sky-400">{{ $inq->user->full_name }}</a>@else{{ $inq->user->full_name }}@endcan
                            @endif
                        </td>
                        <td class="whitespace-nowrap">{{ jdate($inq->created_at, 'Y/m/d H:i') }}</td>
                        <td><x-status-badge :color="$inq->statusColor()" :label="$inq->statusLabel()" /></td>
                        <td><a href="{{ route('admin.inquiries.show', $inq) }}" class="btn-primary btn-sm">بررسی</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-slate-500">استعلامی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $inquiries->links() }}
@endsection
