// "Add to cart" without leaving the page (falls back to a normal form post without JS).
import axios from 'axios';
import { toast } from './ui';

export function initCart() {
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-ajax-cart]');
        if (!form) return;
        e.preventDefault();

        const button = form.querySelector('button[type=submit]');
        button.disabled = true;
        try {
            const { data } = await axios.post(form.action, new FormData(form), { headers: { Accept: 'application/json' } });
            document.querySelectorAll('[data-cart-count]').forEach((badge) => {
                badge.textContent = data.count;
                badge.hidden = data.count < 1;
            });
            toast(data.message, 'success');
        } catch (err) {
            toast(err.response?.data?.message || 'خطا در افزودن به سبد خرید', 'error');
        } finally {
            button.disabled = false;
        }
    });
}
