@extends('admin.layout')

@section('title', 'کاربران')

@section('content')
    <form method="GET" data-clean class="flex flex-wrap gap-2 mb-4">
        <input name="q" value="{{ request('q') }}" placeholder="نام، نام کاربری، ایمیل، موبایل یا کد داخلی..." class="form-input py-2! text-xs max-w-72">
        <select name="role" data-autosubmit class="form-input py-2! text-xs w-auto">
            <option value="">همه نقش‌ها</option>
            @foreach ($roles as $r)<option value="{{ $r->id }}" @selected(request('role') == $r->id)>{{ $r->label }}</option>@endforeach
        </select>
        <select name="status" data-autosubmit class="form-input py-2! text-xs w-auto">
            <option value="">همه</option>
            <option value="staff" @selected(request('status') === 'staff')>کارکنان (غیر مشتری)</option>
            <option value="banned" @selected(request('status') === 'banned')>مسدود شده</option>
        </select>
        <button class="btn-ghost btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>کاربر</th><th>کد داخلی</th><th>ایمیل / موبایل</th><th>نقش</th><th>سفارش / استعلام</th><th>ثبت‌نام</th><th>آخرین ورود</th><th></th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="{{ $user->isBanned() ? 'opacity-60' : '' }}">
                        <td class="whitespace-nowrap">
                            <div class="font-bold text-white">{{ $user->full_name }}</div>
                            <div class="text-[10px] text-slate-500 font-mono" dir="ltr">{{ '@'.$user->username }}</div>
                        </td>
                        <td class="font-mono text-[11px] text-slate-400">{{ $user->internal_code }}</td>
                        <td class="text-[11px] font-mono" dir="ltr">{{ $user->email }}<br>{{ $user->phone }}</td>
                        <td>
                            <x-status-badge :color="$user->isSuperAdmin() ? 'rose' : ($user->canAccessAdmin() ? 'amber' : 'sky')" :label="$user->role?->label ?? '—'" />
                            @if ($user->isBanned())<x-status-badge color="rose" label="مسدود" />@endif
                        </td>
                        <td>{{ fa_num($user->orders_count) }} / {{ fa_num($user->inquiries_count) }}</td>
                        <td class="whitespace-nowrap">{{ jdate($user->created_at) }}</td>
                        <td class="whitespace-nowrap">{{ $user->last_login_at ? jdate($user->last_login_at, 'Y/m/d H:i') : '—' }}</td>
                        <td><a href="{{ route('admin.users.show', $user) }}" class="btn-primary btn-sm">مدیریت</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-10 text-slate-500">کاربری یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
@endsection
