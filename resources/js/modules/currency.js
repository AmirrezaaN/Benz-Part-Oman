// Toman ⇄ Omani Rial switch. Every price is rendered in both currencies on the server;
// this only flips a data attribute on <html>, and remembers the choice in a cookie.

const LABELS = { toman: 'تومان', omr: 'ریال عمان' };

function apply(currency) {
    document.documentElement.dataset.currency = currency;
    document.querySelectorAll('[data-currency-label]').forEach((el) => (el.textContent = LABELS[currency]));
    document.cookie = `currency=${currency}; path=/; max-age=31536000; SameSite=Lax`;
}

export function initCurrency() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-currency-toggle]');
        if (!btn) return;
        const next = document.documentElement.dataset.currency === 'omr' ? 'toman' : 'omr';
        apply(next);
    });
}
