@extends('admin.layout')

@section('title', 'برندهای خودرو')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-xs text-slate-400">ساختار: برند ← مدل ← شاسی. برندهای «ویژه» در انتخابگر شاسی هدر سایت نمایش داده می‌شوند.</p>
        <a href="{{ route('admin.brands.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> برند جدید</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th></th><th>برند</th><th>مدل‌ها</th><th>محصولات</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
                @foreach ($brands as $brand)
                    <tr>
                        <td class="w-12">
                            <span class="w-9 h-9 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden">
                                @if ($brand->logoUrl())<img src="{{ $brand->logoUrl() }}" alt="" class="w-7 h-7 object-contain">@else<span class="font-black text-sky-400">{{ mb_substr($brand->name, 0, 1) }}</span>@endif
                            </span>
                        </td>
                        <td><span class="font-bold text-white">{{ $brand->name }}</span> <span class="text-slate-500">{{ $brand->name_fa }}</span></td>
                        <td><a href="{{ route('admin.car-models.index', ['brand' => $brand->id]) }}" class="text-sky-400">{{ fa_num($brand->car_models_count) }} مدل</a></td>
                        <td>{{ fa_num($brand->products_count) }}</td>
                        <td>
                            <x-status-badge :color="$brand->is_active ? 'emerald' : 'slate'" :label="$brand->is_active ? 'فعال' : 'غیرفعال'" />
                            @if ($brand->is_featured)<x-status-badge color="amber" label="ویژه" />@endif
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="text-sky-400 ml-3"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" data-confirm="برند «{{ $brand->name }}» و همه مدل‌ها و شاسی‌های آن حذف شود؟" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-rose-400"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
