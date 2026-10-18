@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-1.5 mt-8 text-xs" aria-label="صفحه‌بندی">
        @if ($paginator->onFirstPage())
            <span class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-800 text-slate-600 flex items-center justify-center"><i class="fa-solid fa-chevron-right"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-300 flex items-center justify-center"><i class="fa-solid fa-chevron-right"></i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-slate-500">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 rounded-lg bg-sky-500 text-slate-950 font-black flex items-center justify-center">{{ fa_num($page) }}</span>
                    @else
                        <a href="{{ $url }}" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-300 flex items-center justify-center">{{ fa_num($page) }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-300 flex items-center justify-center"><i class="fa-solid fa-chevron-left"></i></a>
        @else
            <span class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-800 text-slate-600 flex items-center justify-center"><i class="fa-solid fa-chevron-left"></i></span>
        @endif
    </nav>
@endif
