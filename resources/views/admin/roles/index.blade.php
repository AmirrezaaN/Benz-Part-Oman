@extends('admin.layout')

@section('title', 'نقش‌ها و دسترسی‌ها')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-xs text-slate-400 leading-6">هر کاربر یک نقش دارد. همه کاربران جدید نقش «مشتری» می‌گیرند. نقش «مدیر کل» فقط متعلق به مالک سایت است و قابل اعطا نیست.</p>
        <a href="{{ route('admin.roles.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> نقش جدید</a>
    </div>
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($roles as $role)
            <div class="card p-5 flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-black text-white">{{ $role->label }}</h2>
                        <div class="text-[10px] text-slate-500 font-mono mt-0.5" dir="ltr">{{ $role->name }}</div>
                    </div>
                    <span class="badge bg-slate-800 text-slate-300 border-slate-700">{{ fa_num($role->users_count) }} کاربر</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 leading-5">{{ $role->description }}</p>
                <div class="flex flex-wrap gap-1 mt-3 flex-1 content-start">
                    @if ($role->isSuperAdmin())
                        <span class="badge bg-rose-500/10 text-rose-300 border-rose-500/30">همه دسترسی‌ها</span>
                    @else
                        @forelse ($role->permissions ?? [] as $perm)
                            <span class="badge bg-slate-800 text-slate-300 border-slate-700">{{ \App\Models\Role::allPermissions()[$perm] ?? $perm }}</span>
                        @empty
                            <span class="text-[11px] text-slate-500">بدون دسترسی مدیریتی</span>
                        @endforelse
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-800">
                    @unless ($role->isSuperAdmin())
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-ghost btn-sm"><i class="fa-solid fa-pen-to-square"></i> ویرایش</a>
                    @endunless
                    <a href="{{ route('admin.users.index', ['role' => $role->id]) }}" class="btn-ghost btn-sm"><i class="fa-solid fa-users"></i> کاربران</a>
                    @unless ($role->is_system)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" data-confirm="نقش «{{ $role->label }}» حذف شود؟ کاربران آن مشتری می‌شوند." class="mr-auto">
                            @csrf @method('DELETE')
                            <button class="text-rose-400 text-xs"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
@endsection
