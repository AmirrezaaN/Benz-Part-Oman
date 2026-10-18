// "Find parts for your car": brand → model → chassis dependent selects.

function fill(select, items, placeholder) {
    select.replaceChildren();
    const first = document.createElement('option');
    first.value = '';
    first.textContent = placeholder;
    select.appendChild(first);
    items.forEach((item) => {
        const opt = document.createElement('option');
        opt.value = item.slug;
        opt.textContent = item.name;
        select.appendChild(opt);
    });
    select.disabled = items.length === 0;
}

export function initVehicleFinder() {
    const form = document.querySelector('[data-vehicle-finder]');
    const source = document.getElementById('vehicle-tree');
    if (!form || !source) return;

    const tree = JSON.parse(source.textContent);
    const brand = form.elements.brand;
    const model = form.elements.model;
    const chassis = form.elements.chassis;

    fill(brand, tree, 'انتخاب برند');
    fill(model, [], 'انتخاب مدل');
    fill(chassis, [], 'انتخاب شاسی');

    brand.addEventListener('change', () => {
        const b = tree.find((x) => x.slug === brand.value);
        fill(model, b ? b.models : [], 'همه مدل‌ها');
        fill(chassis, [], 'انتخاب شاسی');
    });

    model.addEventListener('change', () => {
        const b = tree.find((x) => x.slug === brand.value);
        const m = b?.models.find((x) => x.slug === model.value);
        fill(chassis, m ? m.chassis : [], 'همه شاسی‌ها');
    });
}
