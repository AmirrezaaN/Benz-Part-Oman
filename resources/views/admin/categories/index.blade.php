@extends('admin.layout')

@section('title', 'بخش‌ها (دسته‌بندی قطعات)')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-xs text-slate-400">بخش‌ها مثل موتور، بدنه، جلوبندی ... — هر محصول به یک بخش تعلق دارد و در منو، صفحه اصلی و فیلترها نمایش داده می‌شود.</p>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> بخش جدید</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table-base">
            <thead><tr><th>ترتیب</th><th>نام</th><th>محصولات</th><th>وضعیت</th><th>صفحه اصلی</th><th></th></tr></thead>
            <tbody>
                @foreach ($categories as $cat)
                    @foreach ([$cat, ...$cat->children] as $row)
                        <tr>
                            <td>{{ fa_num($row->sort_order) }}</td>
                            <td class="font-bold text-white">
                                @if ($row->parent_id)<span class="text-slate-600 ml-2">└</span>@endif
                                <i class="fa-solid {{ $row->icon ?: 'fa-gear' }} text-sky-400 ml-1.5 w-4"></i>{{ $row->name }}
                            </td>
                            <td>{{ fa_num($row->products_count) }}</td>
                            <td><x-status-badge :color="$row->is_active ? 'emerald' : 'slate'" :label="$row->is_active ? 'فعال' : 'غیرفعال'" /></td>
                            <td>{!! $row->show_on_home ? '<i class="fa-solid fa-check text-emerald-400"></i>' : '<i class="fa-solid fa-minus text-slate-600"></i>' !!}</td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.categories.edit', $row) }}" class="text-sky-400 ml-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $row) }}" data-confirm="بخش «{{ $row->name }}» حذف شود؟" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-400"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
