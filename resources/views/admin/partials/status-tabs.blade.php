{{-- Status filter tabs. Expects: $statuses (config array), $counts, $route --}}
<div class="flex flex-wrap gap-1.5 mb-4 text-xs">
    <a href="{{ route($route, request()->except('status', 'page')) }}"
       class="px-3 py-1.5 rounded-lg border {{ ! request('status') ? 'bg-sky-500/10 border-sky-500/30 text-sky-400' : 'border-slate-800 text-slate-400 hover:bg-slate-800' }}">
        همه <span class="text-slate-500">({{ fa_num($counts->sum()) }})</span>
    </a>
    @foreach ($statuses as $key => $status)
        <a href="{{ route($route, array_merge(request()->except('page'), ['status' => $key])) }}"
           class="px-3 py-1.5 rounded-lg border {{ request('status') === $key ? 'bg-sky-500/10 border-sky-500/30 text-sky-400' : 'border-slate-800 text-slate-400 hover:bg-slate-800' }}">
            {{ $status['label'] }} <span class="text-slate-500">({{ fa_num($counts[$key] ?? 0) }})</span>
        </a>
    @endforeach
</div>
