@extends('admin.layout')

@section('title', 'مدل‌های خودرو')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
        <form method="GET" data-clean>
            <select name="brand" data-autosubmit class="form-input py-2! text-xs w-auto">
                <option value="">همه برندها</option>
                @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand') == $b->id)>{{ $b->name }}</option>@endforeach
            </select>
        </form>
        <a href="{{ route('admin.car-models.create', ['brand' => request('brand')]) }}" class="btn-primary"><i class="fa-solid fa-plus"></i> مدل جدید</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>برند</th><th>مدل</th><th>شاسی‌ها</th><th>ترتیب</th><th></th></tr></thead>
            <tbody>
                @forelse ($models as $model)
                    <tr>
                        <td>{{ $model->brand?->name }}</td>
                        <td class="font-bold text-white">{{ $model->name }} <span class="text-slate-500 font-normal">{{ $model->name_fa }}</span></td>
                        <td><a href="{{ route('admin.chassis.index', ['model' => $model->id]) }}" class="text-sky-400">{{ fa_num($model->chassis_count) }} شاسی</a></td>
                        <td>{{ fa_num($model->sort_order) }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.chassis.create', ['model' => $model->id]) }}" class="text-emerald-400 ml-3" title="افزودن شاسی"><i class="fa-solid fa-plus"></i></a>
                            <a href="{{ route('admin.car-models.edit', $model) }}" class="text-sky-400 ml-3"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="{{ route('admin.car-models.destroy', $model) }}" data-confirm="مدل «{{ $model->name }}» و شاسی‌های آن حذف شود؟" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-rose-400"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">مدلی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $models->links() }}
@endsection
