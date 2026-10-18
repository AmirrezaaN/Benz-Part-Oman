@extends('admin.layout')

@section('title', 'گزارش فعالیت‌ها')

@section('content')
    <div class="flex flex-wrap gap-1.5 mb-4 text-xs">
        @foreach (['' => 'همه', 'product' => 'محصولات', 'user' => 'کاربران', 'role' => 'نقش‌ها', 'order' => 'سفارش‌ها', 'inquiry' => 'استعلام‌ها', 'settings' => 'تنظیمات', 'auth' => 'ورود مدیران'] as $key => $label)
            <a href="{{ route('admin.activity.index', array_filter(['action' => $key])) }}"
               class="px-3 py-1.5 rounded-lg border {{ request('action', '') === $key ? 'bg-sky-500/10 border-sky-500/30 text-sky-400' : 'border-slate-800 text-slate-400 hover:bg-slate-800' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>زمان</th><th>کاربر</th><th>عملیات</th><th>شرح</th><th>IP</th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap">{{ jdate($log->created_at, 'Y/m/d H:i') }}</td>
                        <td class="whitespace-nowrap">
                            @if ($log->user)<a href="{{ route('admin.users.show', $log->user) }}" class="hover:text-sky-400">{{ $log->user->full_name }}</a>@else — @endif
                        </td>
                        <td class="font-mono text-[11px] text-slate-400" dir="ltr">{{ $log->action }}</td>
                        <td>{{ $log->description }}</td>
                        <td class="font-mono text-[11px] text-slate-500" dir="ltr">{{ $log->ip }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-10 text-slate-500">فعالیتی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
@endsection
