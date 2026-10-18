import './bootstrap';
import { initUi } from './modules/ui';
import {
    Chart, LineController, BarController, DoughnutController, LineElement, BarElement, ArcElement, PointElement,
    LinearScale, CategoryScale, Filler, Tooltip, Legend,
} from 'chart.js';

Chart.register(LineController, BarController, DoughnutController, LineElement, BarElement, ArcElement, PointElement,
    LinearScale, CategoryScale, Filler, Tooltip, Legend);

Chart.defaults.font.family = 'Vazirmatn, Tahoma, sans-serif';
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(148,163,184,0.12)';

function initCharts() {
    const source = document.getElementById('stats-data');
    if (!source) return;
    const data = JSON.parse(source.textContent);

    const line = (id, datasets) => {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'line',
            data: { labels: data.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { ticks: { maxRotation: 0, autoSkipPadding: 12 } } },
                elements: { line: { tension: 0.35 }, point: { radius: 0, hoverRadius: 4 } },
            },
        });
    };

    line('chart-visits', [
        { label: 'بازدیدکننده یکتا', data: data.uniques, borderColor: '#38bdf8', backgroundColor: 'rgba(56,189,248,.12)', fill: true },
        { label: 'بازدید صفحات', data: data.hits, borderColor: '#818cf8', backgroundColor: 'transparent' },
    ]);
    line('chart-activity', [
        { label: 'ثبت‌نام', data: data.users, borderColor: '#34d399', backgroundColor: 'transparent' },
        { label: 'استعلام', data: data.inquiries, borderColor: '#fbbf24', backgroundColor: 'transparent' },
        { label: 'سفارش', data: data.orders, borderColor: '#f472b6', backgroundColor: 'transparent' },
    ]);

    const revenue = document.getElementById('chart-revenue');
    if (revenue) {
        new Chart(revenue, {
            type: 'bar',
            data: { labels: data.labels, datasets: [{ label: 'فروش (تومان)', data: data.revenue, backgroundColor: 'rgba(56,189,248,.55)', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    }

    document.querySelectorAll('canvas[data-doughnut]').forEach((el) => {
        const d = JSON.parse(el.dataset.doughnut);
        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: d.labels,
                datasets: [{ data: d.values, backgroundColor: ['#fbbf24', '#38bdf8', '#34d399', '#64748b', '#818cf8', '#f472b6', '#fb7185'], borderWidth: 0 }],
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } } },
        });
    });
}

/** Filter + "select all" for the compatible-chassis picker in the product form. */
function initChassisPicker() {
    const picker = document.querySelector('[data-chassis-picker]');
    if (!picker) return;

    const counter = picker.querySelector('[data-chassis-count]');
    const update = () => (counter.textContent = picker.querySelectorAll('input[name="chassis[]"]:checked').length);

    picker.querySelector('[data-chassis-filter]')?.addEventListener('input', (e) => {
        const q = e.target.value.trim().toLowerCase();
        picker.querySelectorAll('[data-chassis-group]').forEach((group) => {
            let visible = 0;
            group.querySelectorAll('[data-chassis-item]').forEach((item) => {
                const match = !q || item.dataset.search.includes(q) || group.dataset.search.includes(q);
                item.hidden = !match;
                if (match) visible++;
            });
            group.hidden = visible === 0;
        });
    });

    picker.addEventListener('change', (e) => {
        if (e.target.matches('[data-select-model]')) {
            e.target.closest('[data-chassis-group]').querySelectorAll('input[name="chassis[]"]').forEach((cb) => (cb.checked = e.target.checked));
        }
        update();
    });
    update();
}

/** Shows the Toman equivalent of an OMR price while typing. */
function initPricePreview() {
    document.querySelectorAll('[data-omr-input]').forEach((input) => {
        const out = document.getElementById(input.dataset.omrInput);
        const rate = Number(input.dataset.rate) || 0;
        const render = () => {
            const v = parseFloat((input.value || '').replace(/[,،\s]/g, ''));
            out.textContent = v && rate ? `≈ ${(Math.round((v * rate) / 1000) * 1000).toLocaleString('fa-IR')} تومان` : '';
        };
        input.addEventListener('input', render);
        render();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initUi();
    initCharts();
    initChassisPicker();
    initPricePreview();
});
