// Live search suggestions in the header. Everything is inserted with textContent (XSS-safe).
import axios from 'axios';

function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = text;
    return node;
}

function renderItem(item) {
    const a = el('a', 'flex items-center gap-3 px-3 py-2.5 hover:bg-slate-800/70 transition border-b border-slate-800/60 last:border-0');
    a.href = item.url;

    const thumb = el('div', 'w-11 h-11 shrink-0 rounded-lg bg-slate-800 overflow-hidden flex items-center justify-center text-slate-600');
    if (item.image) {
        const img = el('img', 'w-full h-full object-cover');
        img.src = item.image;
        img.alt = '';
        img.loading = 'lazy';
        thumb.appendChild(img);
    } else {
        thumb.appendChild(el('i', 'fa-solid fa-gear'));
    }

    const body = el('div', 'min-w-0 flex-1');
    body.appendChild(el('div', 'text-xs font-bold text-white truncate', item.name));
    const meta = el('div', 'text-[10px] text-slate-400 font-mono truncate mt-0.5', [item.part_number, item.chassis].filter(Boolean).join(' • '));
    body.appendChild(meta);

    const price = el('div', 'text-[11px] font-black text-sky-400 whitespace-nowrap');
    if (item.price_toman) {
        price.appendChild(el('span', 'price-toman', `${item.price_toman} تومان`));
    }
    if (item.price_omr) {
        price.appendChild(el('span', 'price-omr', `${item.price_omr} OMR`));
    }
    if (!item.price_toman && !item.price_omr) {
        price.appendChild(el('span', 'text-slate-400 font-normal', 'استعلام'));
    }

    a.append(thumb, body, price);
    return a;
}

export function initSearch() {
    document.querySelectorAll('[data-search]').forEach((wrap) => {
        const input = wrap.querySelector('input[name=q]');
        const box = wrap.querySelector('[data-search-results]');
        if (!input || !box) return;

        let timer;
        let controller;

        const close = () => (box.hidden = true);

        input.addEventListener('input', () => {
            clearTimeout(timer);
            const q = input.value.trim();
            if (q.length < 2) return close();

            timer = setTimeout(async () => {
                controller?.abort();
                controller = new AbortController();
                try {
                    const { data } = await axios.get(wrap.dataset.search, { params: { q }, signal: controller.signal });
                    box.replaceChildren();
                    if (!data.items.length) {
                        box.appendChild(el('div', 'px-4 py-5 text-center text-xs text-slate-400', 'نتیجه‌ای پیدا نشد. می‌توانید استعلام بگیرید.'));
                    } else {
                        data.items.forEach((item) => box.appendChild(renderItem(item)));
                        const all = el('a', 'block text-center text-[11px] font-bold text-sky-400 py-2.5 bg-slate-950/60 hover:bg-slate-800', 'مشاهده همه نتایج');
                        all.href = data.all_url;
                        box.appendChild(all);
                    }
                    box.hidden = false;
                } catch (e) {
                    if (!axios.isCancel(e)) close();
                }
            }, 250);
        });

        input.addEventListener('keydown', (e) => e.key === 'Escape' && close());
        document.addEventListener('click', (e) => !wrap.contains(e.target) && close());
    });
}
