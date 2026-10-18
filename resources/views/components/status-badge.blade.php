@props(['color' => 'slate', 'label'])
@php
    $classes = [
        'amber' => 'bg-amber-500/10 text-amber-300 border-amber-500/30',
        'sky' => 'bg-sky-500/10 text-sky-300 border-sky-500/30',
        'emerald' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
        'indigo' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/30',
        'violet' => 'bg-violet-500/10 text-violet-300 border-violet-500/30',
        'rose' => 'bg-rose-500/10 text-rose-300 border-rose-500/30',
        'slate' => 'bg-slate-500/10 text-slate-300 border-slate-500/30',
    ][$color] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/30';
@endphp
<span {{ $attributes->class("badge $classes") }}>{{ $label }}</span>
