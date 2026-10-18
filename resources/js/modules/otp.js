// Resend countdown for verification-code pages.

export function initOtp() {
    document.querySelectorAll('[data-countdown]').forEach((btn) => {
        let seconds = Number(btn.dataset.countdown) || 0;
        const label = btn.querySelector('[data-countdown-label]');
        const original = label?.textContent;

        const tick = () => {
            if (seconds <= 0) {
                btn.disabled = false;
                if (label) label.textContent = original;
                return;
            }
            btn.disabled = true;
            if (label) label.textContent = `ارسال مجدد تا ${seconds} ثانیه`;
            seconds -= 1;
            setTimeout(tick, 1000);
        };
        tick();
    });

    // Auto-submit once all digits are entered
    document.querySelectorAll('input[data-otp]').forEach((input) => {
        input.focus();
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(0, Number(input.maxLength) || 6);
            if (input.value.length === (Number(input.maxLength) || 6)) {
                input.form.requestSubmit ? input.form.requestSubmit() : input.form.submit();
            }
        });
    });
}
