{{-- Every item is shown only when its setting has a value (empty setting = item and icon hidden) --}}
@if (setting('announcement') || setting('delivery_days') || setting('phone_oman'))
    <div class="bg-slate-900 border-b border-slate-800 text-slate-300 text-[11px] py-1.5">
        <div class="container-x flex items-center justify-between gap-3">
            @if (setting('announcement'))
                <span class="flex items-center gap-2 truncate">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <span class="truncate">{{ setting('announcement') }}</span>
                </span>
            @else
                <span></span>
            @endif
            <div class="flex items-center gap-4 text-slate-400 shrink-0">
                @if (setting('delivery_days'))
                    <span class="hidden sm:inline"><i class="fa-solid fa-truck-fast text-sky-400 ml-1"></i> تحویل {{ setting('delivery_days') }} در ایران</span>
                @endif
                @if (setting('phone_oman'))
                    <a href="{{ whatsapp_link() }}" target="_blank" rel="noopener" class="hover:text-white transition">
                        <i class="fa-brands fa-whatsapp text-emerald-400 ml-1"></i>
                        <span class="hidden sm:inline">پشتیبانی مسقط:</span>
                        <span dir="ltr">{{ setting('phone_oman') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
