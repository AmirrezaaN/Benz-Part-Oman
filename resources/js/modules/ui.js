// Generic UI behaviours shared by the storefront and the admin panel.

export function toast(message, type = 'success') {
    let holder = document.getElementById('toast-holder');
    if (!holder) {
        holder = document.createElement('div');
        holder.id = 'toast-holder';
        holder.className = 'fixed top-4 left-4 right-4 sm:right-auto z-[100] flex flex-col gap-2 sm:w-96';
        document.body.appendChild(holder);
    }
    const colors = {
        success: 'bg-emerald-950/95 border-emerald-500/40 text-emerald-200',
        error: 'bg-rose-950/95 border-rose-500/40 text-rose-200',
        info: 'bg-sky-950/95 border-sky-500/40 text-sky-200',
    };
    const el = document.createElement('div');
    el.className = `toast border rounded-xl px-4 py-3 text-xs font-medium shadow-2xl backdrop-blur ${colors[type] || colors.info}`;
    el.textContent = message;
    holder.appendChild(el);
    setTimeout(() => el.remove(), 5000);
}

function initDrawers() {
    const setState = (id, open) => {
        document.querySelectorAll(`[data-drawer="${id}"], [data-drawer-overlay="${id}"]`).forEach((el) => {
            el.dataset.open = open ? 'true' : 'false';
        });
        document.body.style.overflow = open ? 'hidden' : '';
    };
    document.addEventListener('click', (e) => {
        const opener = e.target.closest('[data-drawer-open]');
        if (opener) {
            e.preventDefault();
            setState(opener.dataset.drawerOpen, true);
            return;
        }
        const closer = e.target.closest('[data-drawer-close], [data-drawer-overlay]');
        if (closer) {
            const id = closer.dataset.drawerClose || closer.dataset.drawerOverlay;
            setState(id, false);
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-drawer][data-open="true"]').forEach((d) => setState(d.dataset.drawer, false));
        }
    });
}

function initDropdowns() {
    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-dropdown-toggle]');
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            const parent = menu.closest('[data-dropdown]');
            if (toggle && parent && parent.contains(toggle)) {
                menu.hidden = !menu.hidden;
            } else if (!parent || !parent.contains(e.target)) {
                menu.hidden = true;
            }
        });
    });
}

function initFlash() {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => el.remove(), 7000);
    });
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-dismiss]');
        if (btn) btn.closest('[data-dismissible]')?.remove();
    });
}

function initConfirm() {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            e.preventDefault();
        }
    });
}

function initAutosubmit() {
    document.addEventListener('change', (e) => {
        const nav = e.target.closest('select[data-navigate]');
        if (nav && nav.value) {
            window.location.href = nav.value;
            return;
        }
    });
    document.addEventListener('change', (e) => {
        const el = e.target.closest('[data-autosubmit]');
        if (!el) return;
        const form = el.form;
        // Changing a parent filter resets the dependent ones
        (el.dataset.resets || '').split(',').filter(Boolean).forEach((name) => {
            const dep = form.elements[name];
            if (dep) dep.value = '';
        });
        form.requestSubmit ? form.requestSubmit() : form.submit();
    });
}

/** Strips empty fields from GET forms so URLs stay clean. */
function initCleanGetForms() {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if ((form.method || '').toLowerCase() !== 'get' || !form.hasAttribute('data-clean')) return;
        Array.from(form.elements).forEach((el) => {
            if (el.name && !el.value) el.disabled = true;
        });
        setTimeout(() => Array.from(form.elements).forEach((el) => (el.disabled = false)), 0);
    });
}

/** Converts Persian/Arabic digits to Latin while typing in [data-latin-digits] inputs. */
function initLatinDigits() {
    const map = { '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4', '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
        '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4', '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9' };
    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!el.matches?.('[data-latin-digits]')) return;
        const fixed = el.value.replace(/[۰-۹٠-٩]/g, (d) => map[d]);
        if (fixed !== el.value) el.value = fixed;
    });
}

function initPasswordToggle() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-toggle-password]');
        if (!btn) return;
        const input = btn.parentElement.querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('i')?.classList.toggle('fa-eye');
        btn.querySelector('i')?.classList.toggle('fa-eye-slash');
    });
}

function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach((tabs) => {
        const buttons = tabs.querySelectorAll('[data-tab]');
        const panels = tabs.querySelectorAll('[data-tab-panel]');
        buttons.forEach((btn) => btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-selected', b === btn ? 'true' : 'false'));
            panels.forEach((p) => (p.hidden = p.dataset.tabPanel !== btn.dataset.tab));
        }));
    });
}

function initImagePreview() {
    document.addEventListener('change', (e) => {
        const input = e.target;
        if (!input.matches?.('input[type=file][data-preview]')) return;
        const target = document.getElementById(input.dataset.preview);
        if (!target) return;
        target.innerHTML = '';
        Array.from(input.files).slice(0, 8).forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-20 h-20 object-cover rounded-lg border border-slate-700';
            target.appendChild(img);
        });
    });
}

export function initUi() {
    initDrawers();
    initDropdowns();
    initFlash();
    initConfirm();
    initAutosubmit();
    initCleanGetForms();
    initLatinDigits();
    initPasswordToggle();
    initTabs();
    initImagePreview();
}
