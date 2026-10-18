@php
    $messages = array_filter([
        'success' => session('success'),
        'error' => session('error'),
        'info' => session('info'),
    ]);
    $styles = [
        'success' => ['bg-emerald-950/95 border-emerald-500/40 text-emerald-200', 'fa-circle-check'],
        'error' => ['bg-rose-950/95 border-rose-500/40 text-rose-200', 'fa-circle-exclamation'],
        'info' => ['bg-sky-950/95 border-sky-500/40 text-sky-200', 'fa-circle-info'],
    ];
@endphp
@if ($messages)
    <div class="fixed top-4 left-4 right-4 sm:right-auto z-[100] flex flex-col gap-2 sm:w-96">
        @foreach ($messages as $type => $message)
            <div data-flash data-dismissible class="toast border rounded-xl px-4 py-3 text-xs font-medium shadow-2xl backdrop-blur flex items-start gap-2.5 {{ $styles[$type][0] }}">
                <i class="fa-solid {{ $styles[$type][1] }} mt-0.5"></i>
                <span class="flex-1 leading-6">{{ $message }}</span>
                <button type="button" data-dismiss class="opacity-60 hover:opacity-100" aria-label="بستن"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endforeach
    </div>
@endif
