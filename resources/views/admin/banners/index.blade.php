@extends('admin.layout')

@section('title', 'اسلایدر صفحه اصلی')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-xs text-slate-400">اسلایدهای بزرگ بالای صفحه اصلی. تصویر پیشنهادی: ۱۶۰۰×۷۰۰ پیکسل. بدون تصویر، طرح گرافیکی پیش‌فرض نمایش داده می‌شود.</p>
        <a href="{{ route('admin.banners.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> اسلاید جدید</a>
    </div>
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($banners as $banner)
            <div class="card overflow-hidden {{ $banner->is_active ? '' : 'opacity-60' }}">
                <div class="h-36 bg-gradient-to-l from-sky-600/30 to-navy-950 relative flex items-end p-4">
                    @if ($banner->imageUrl())<img src="{{ $banner->imageUrl() }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-50">@endif
                    <div class="relative">
                        @if ($banner->badge)<span class="badge bg-sky-500/20 text-sky-300 border-sky-500/30">{{ $banner->badge }}</span>@endif
                        <h2 class="text-sm font-black text-white mt-1.5">{{ $banner->title }}</h2>
                    </div>
                </div>
                <div class="p-4 flex items-center justify-between text-xs">
                    <span class="text-slate-500">ترتیب {{ fa_num($banner->sort_order) }} · {{ $banner->is_active ? 'فعال' : 'غیرفعال' }}</span>
                    <span>
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="text-sky-400 ml-3"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" data-confirm="این اسلاید حذف شود؟" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-rose-400"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    </span>
                </div>
            </div>
        @empty
            <p class="text-xs text-slate-500">اسلایدی ثبت نشده؛ یک اسلاید پیش‌فرض نمایش داده می‌شود.</p>
        @endforelse
    </div>
@endsection
