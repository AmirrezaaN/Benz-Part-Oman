@extends('admin.layout')

@section('title', 'کاربر: '.$user->full_name)

@section('content')
    <div class="grid xl:grid-cols-3 gap-5">
        {{-- Profile --}}
        <div class="space-y-5">
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center text-xl font-black">{{ mb_substr($user->full_name, 0, 1) }}</span>
                    <div>
                        <div class="text-base font-black text-white">{{ $user->full_name }}</div>
                        <div class="text-[11px] text-slate-500 font-mono" dir="ltr">{{ '@'.$user->username }}</div>
                        <x-status-badge class="mt-1" :color="$user->isSuperAdmin() ? 'rose' : ($user->canAccessAdmin() ? 'amber' : 'sky')" :label="$user->role?->label ?? '—'" />
                    </div>
                </div>
                <dl class="text-xs space-y-2.5">
                    @foreach ([
                        'کد داخلی (غیرقابل تغییر)' => $user->internal_code,
                        'ایمیل' => $user->email ? $user->email.($user->email_verified_at ? ' ✓' : '') : null,
                        'موبایل' => $user->phone ? $user->phone.($user->phone_verified_at ? ' ✓' : '') : null,
                        'تاریخ ثبت‌نام' => jdate($user->created_at, 'Y/m/d H:i'),
                        'آخرین ورود' => $user->last_login_at ? jdate($user->last_login_at, 'Y/m/d H:i') : null,
                        'IP آخرین ورود' => $user->last_login_ip,
                        'آدرس' => trim($user->province.' '.$user->city.' '.$user->address) ?: null,
                        'کد پستی' => $user->postal_code,
                    ] as $label => $value)
                        <div class="flex justify-between gap-3 border-b border-slate-800/70 pb-2">
                            <dt class="text-slate-500 shrink-0">{{ $label }}</dt>
                            <dd class="text-white text-left break-all">{{ $value ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
                @if ($user->isBanned())
                    <div class="mt-4 bg-rose-950/50 border border-rose-500/30 rounded-xl p-3 text-xs text-rose-200">
                        <i class="fa-solid fa-ban ml-1"></i> مسدود از {{ jdate($user->banned_at, 'Y/m/d H:i') }}
                        @if ($user->ban_reason)<div class="mt-1 text-rose-300/80">دلیل: {{ $user->ban_reason }}</div>@endif
                    </div>
                @endif
            </div>

            {{-- Role & access --}}
            <div class="card p-5">
                <h2 class="text-sm font-bold text-white mb-3"><i class="fa-solid fa-user-shield text-amber-300 ml-1.5"></i>نقش و دسترسی</h2>
                @if (! $canManage)
                    <p class="text-xs text-slate-400 leading-6">
                        @if ($user->isSuperAdmin()) این حساب مدیر کل سایت است و قابل تغییر نیست.
                        @elseif ($user->is(auth()->user())) نقش حساب خودتان را نمی‌توانید تغییر دهید.
                        @else شما اجازه مدیریت این کاربر را ندارید.
                        @endif
                    </p>
                @else
                    @can('users.change-role')
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex gap-2">
                            @csrf @method('PUT')
                            <select name="role_id" class="form-input">
                                @foreach ($assignableRoles as $role)
                                    <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->label }}</option>
                                @endforeach
                            </select>
                            <button class="btn-primary"><i class="fa-solid fa-check"></i></button>
                        </form>
                        <p class="form-hint mt-2">فقط نقش‌هایی نمایش داده می‌شوند که دسترسی‌هایشان زیرمجموعه دسترسی‌های شماست.</p>
                    @endcan

                    @can('users.ban')
                        <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="mt-4 pt-4 border-t border-slate-800 space-y-2"
                              data-confirm="{{ $user->isBanned() ? 'حساب این کاربر فعال شود؟' : 'حساب این کاربر مسدود شود؟ کاربر بلافاصله از سایت خارج می‌شود.' }}">
                            @csrf @method('PUT')
                            @if ($user->isBanned())
                                <button class="btn-success w-full"><i class="fa-solid fa-unlock"></i> رفع مسدودی</button>
                            @else
                                <input name="ban_reason" maxlength="255" class="form-input text-xs" placeholder="دلیل مسدودسازی (اختیاری)">
                                <button class="btn-danger w-full"><i class="fa-solid fa-ban"></i> مسدود کردن حساب</button>
                            @endif
                        </form>
                    @endcan
                @endif
                <div class="mt-4 pt-4 border-t border-slate-800">
                    <div class="text-[11px] text-slate-500 mb-2">دسترسی‌های فعلی:</div>
                    <div class="flex flex-wrap gap-1">
                        @forelse ($user->permissionKeys() as $perm)
                            <span class="badge bg-slate-800 text-slate-300 border-slate-700">{{ \App\Models\Role::allPermissions()[$perm] ?? $perm }}</span>
                        @empty
                            <span class="text-[11px] text-slate-500">بدون دسترسی مدیریتی (مشتری)</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity --}}
        <div class="xl:col-span-2 space-y-5 min-w-0">
            <div class="card overflow-x-auto">
                <h2 class="text-sm font-bold text-white px-4 py-3.5">سفارش‌ها و خریدها ({{ fa_num($orders->count()) }})</h2>
                <table class="table-base">
                    <thead><tr><th>کد</th><th>تاریخ</th><th>اقلام</th><th>مبلغ</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="font-mono">@can('orders.view')<a href="{{ route('admin.orders.show', $order) }}" class="text-sky-400">{{ $order->code }}</a>@else{{ $order->code }}@endcan</td>
                                <td>{{ jdate($order->created_at) }}</td>
                                <td>{{ fa_num($order->items_count) }}</td>
                                <td>{{ format_toman($order->total_toman) }} ت</td>
                                <td><x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-500">سفارشی ندارد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card overflow-x-auto">
                <h2 class="text-sm font-bold text-white px-4 py-3.5">استعلام‌ها ({{ fa_num($inquiries->count()) }})</h2>
                <table class="table-base">
                    <thead><tr><th>کد</th><th>قطعه</th><th>تاریخ</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        @forelse ($inquiries as $inq)
                            <tr>
                                <td class="font-mono">@can('inquiries.view')<a href="{{ route('admin.inquiries.show', $inq) }}" class="text-sky-400">{{ $inq->code }}</a>@else{{ $inq->code }}@endcan</td>
                                <td class="max-w-xs truncate">{{ $inq->part_name }}</td>
                                <td>{{ jdate($inq->created_at) }}</td>
                                <td><x-status-badge :color="$inq->statusColor()" :label="$inq->statusLabel()" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-6 text-slate-500">استعلامی ندارد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activity->isNotEmpty())
                <div class="card overflow-x-auto">
                    <h2 class="text-sm font-bold text-white px-4 py-3.5">فعالیت‌های مدیریتی این کاربر</h2>
                    <table class="table-base">
                        <tbody>
                            @foreach ($activity as $log)
                                <tr><td class="whitespace-nowrap text-slate-500">{{ jdate($log->created_at, 'Y/m/d H:i') }}</td><td>{{ $log->description }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
