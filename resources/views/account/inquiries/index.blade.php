@extends('account.layout')

@section('title', 'استعلام‌های من')

@section('account')
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
            <h1 class="text-sm font-bold text-white">استعلام‌های من</h1>
            <a href="{{ route('inquiries.create') }}" class="btn-primary btn-sm"><i class="fa-solid fa-plus"></i> استعلام جدید</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>کد</th><th>قطعه</th><th>تاریخ</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                    @forelse ($inquiries as $inq)
                        <tr>
                            <td class="font-mono">{{ $inq->code }}</td>
                            <td class="max-w-xs truncate">{{ $inq->part_name }}</td>
                            <td class="whitespace-nowrap">{{ jdate($inq->created_at) }}</td>
                            <td><x-status-badge :color="$inq->statusColor()" :label="$inq->statusLabel()" /></td>
                            <td><a href="{{ route('account.inquiries.show', $inq) }}" class="text-sky-400 font-bold whitespace-nowrap">مشاهده</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-10 text-slate-500">هنوز استعلامی ثبت نکرده‌اید.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $inquiries->links() }}
@endsection
