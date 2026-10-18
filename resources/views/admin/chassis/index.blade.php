@extends('admin.layout')

@section('title', 'شاسی‌ها (اتاق خودرو)')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
        <form method="GET" data-clean class="flex gap-2">
            <input name="q" value="{{ request('q') }}" dir="ltr" placeholder="W213" class="form-input py-2! text-xs w-32 text-left">
            <select name="model" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه مدل‌ها</option>
                @foreach ($brands as $b)
                    <optgroup label="{{ $b->name }}">
                        @foreach ($b->carModels as $m)<option value="{{ $m->id }}" @selected(request('model') == $m->id)>{{ $m->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
            <button class="btn-ghost btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <a href="{{ route('admin.chassis.create', ['model' => request('model')]) }}" class="btn-primary"><i class="fa-solid fa-plus"></i> شاسی جدید</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>برند / مدل</th><th>کد شاسی</th><th>عنوان</th><th>سال تولید</th><th>محصولات</th><th></th></tr></thead>
            <tbody>
                @forelse ($items as $c)
                    <tr>
                        <td>{{ $c->carModel?->brand?->name }} · {{ $c->carModel?->name }}</td>
                        <td class="font-mono font-bold text-sky-400" dir="ltr">{{ $c->code }}</td>
                        <td>{{ $c->title ?: '—' }}</td>
                        <td dir="ltr" class="text-right">{{ $c->year_from ?: '?' }} - {{ $c->year_to ?: 'now' }}</td>
                        <td>{{ fa_num($c->products_count) }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.chassis.edit', $c) }}" class="text-sky-400 ml-3"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="{{ route('admin.chassis.destroy', $c) }}" data-confirm="شاسی «{{ $c->code }}» حذف شود؟" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-rose-400"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-slate-500">شاسی‌ای یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
@endsection
