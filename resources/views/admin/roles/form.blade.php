@extends('admin.layout')

@section('title', $role->exists ? 'ویرایش نقش: '.$role->label : 'نقش جدید')

@section('content')
    @php($selected = old('permissions', $role->permissions ?? []))
    <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="max-w-4xl space-y-5">
        @csrf
        @if ($role->exists) @method('PUT') @endif

        <div class="card p-5 grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label" for="label">نام نقش *</label>
                <input id="label" name="label" value="{{ old('label', $role->label) }}" required maxlength="100" class="form-input" placeholder="مثلاً: انباردار">
            </div>
            @unless ($role->exists)
                <div>
                    <label class="form-label" for="name">شناسه انگلیسی (اختیاری)</label>
                    <input id="name" name="name" value="{{ old('name') }}" maxlength="50" dir="ltr" class="form-input text-left font-mono" placeholder="warehouse">
                </div>
            @endunless
            <div class="sm:col-span-2">
                <label class="form-label" for="description">توضیحات</label>
                <input id="description" name="description" value="{{ old('description', $role->description) }}" maxlength="255" class="form-input">
            </div>
        </div>

        @if ($role->name === \App\Models\Role::CUSTOMER)
            <p class="card p-4 text-xs text-amber-300">نقش مشتری هیچ دسترسی مدیریتی نمی‌گیرد.</p>
        @else
            <div class="grid md:grid-cols-2 gap-4">
                @foreach (config('permissions.groups') as $group => $permissions)
                    <div class="card p-5">
                        <h2 class="text-sm font-bold text-white mb-3">{{ $group }}</h2>
                        <div class="space-y-2.5">
                            @foreach ($permissions as $key => $label)
                                <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" class="form-check" @checked(in_array($key, $selected, true))>
                                    <span>{{ $label }}</span>
                                    <span class="text-[10px] text-slate-600 font-mono mr-auto" dir="ltr">{{ $key }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-500">نکته: بدون «ورود به پنل مدیریت»، کاربر هیچ‌کدام از بخش‌های پنل را نمی‌بیند. پس از ذخیره، کاربران این نقش باید دوباره وارد شوند.</p>
        @endif

        <div class="flex gap-2">
            <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره نقش</button>
            <a href="{{ route('admin.roles.index') }}" class="btn-ghost">انصراف</a>
        </div>
    </form>
@endsection
