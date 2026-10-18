@extends('admin.layout')

@section('title', 'محصولات')

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <form method="GET" data-clean class="flex flex-wrap gap-2 flex-1">
            <input name="q" value="{{ request('q') }}" placeholder="نام، پارت نامبر، شاسی..." class="form-input py-2! text-xs max-w-56">
            <select name="category" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه بخش‌ها</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <select name="brand" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه برندها</option>
                @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand') == $b->id)>{{ $b->name }}</option>@endforeach
            </select>
            <select name="status" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" @selected(request('status') === 'active')>فعال</option>
                <option value="inactive" @selected(request('status') === 'inactive')>غیرفعال</option>
                <option value="featured" @selected(request('status') === 'featured')>ویژه</option>
            </select>
            <select name="stock" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه موجودی‌ها</option>
                <option value="low" @selected(request('stock') === 'low')>ناموجود / کم</option>
            </select>
            <button class="btn-ghost btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        @can('products.create')
            <a href="{{ route('admin.products.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> محصول جدید</a>
        @endcan
    </div>

    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr><th></th><th>محصول</th><th>برند / بخش</th><th>شاسی</th><th>موجودی</th><th>قیمت</th><th>وضعیت</th><th>بازدید</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td class="w-14"><div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-950 border border-slate-800"><x-product-image :product="$p" class="[&_i]:text-lg! [&_span]:hidden" /></div></td>
                        <td class="max-w-xs">
                            <div class="font-bold text-white truncate">{{ $p->name }}</div>
                            @if ($p->part_number)<div class="text-[10px] text-slate-500 font-mono" dir="ltr">{{ $p->part_number }}</div>@endif
                        </td>
                        <td class="whitespace-nowrap">{{ $p->brand?->name ?? '—' }}<div class="text-[10px] text-slate-500">{{ $p->category?->name }}</div></td>
                        <td>{{ fa_num($p->chassis_count) }}</td>
                        <td class="{{ $p->quantity === 0 ? 'text-rose-400 font-bold' : ($p->quantity !== null && $p->quantity <= 2 ? 'text-amber-300 font-bold' : '') }}">{{ $p->quantity === null ? '—' : fa_num($p->quantity) }}</td>
                        <td class="whitespace-nowrap">
                            @if ($p->price_omr !== null)<div dir="ltr" class="text-left">{{ format_omr($p->price_omr) }} OMR</div>@endif
                            @if ($p->tomanPrice())<div class="text-[10px] text-slate-500">{{ format_toman($p->tomanPrice()) }} ت{{ $p->price_toman ? ' (دستی)' : '' }}</div>@endif
                            @if (! $p->hasPrice())<span class="text-slate-500">استعلامی</span>@endif
                        </td>
                        <td class="whitespace-nowrap">
                            <x-status-badge :color="$p->is_active ? 'emerald' : 'slate'" :label="$p->is_active ? 'فعال' : 'غیرفعال'" />
                            @if ($p->is_featured)<x-status-badge color="amber" label="ویژه" />@endif
                        </td>
                        <td>{{ fa_num($p->views) }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('products.show', $p) }}" target="_blank" class="text-slate-400 hover:text-white ml-2" title="مشاهده"><i class="fa-solid fa-eye"></i></a>
                            @can('products.edit')<a href="{{ route('admin.products.edit', $p) }}" class="text-sky-400 hover:text-sky-300" title="ویرایش"><i class="fa-solid fa-pen-to-square"></i></a>@endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-10 text-slate-500">محصولی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $products->links() }}
@endsection
