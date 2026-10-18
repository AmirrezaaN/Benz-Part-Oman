@php
    $selectedBrand = $brands->firstWhere('slug', $filters['brand'] ?? null);
@endphp
<form method="GET" action="{{ url()->current() }}" data-clean class="p-4 lg:p-0 space-y-4">
    @if (isset($filters['sort']))
        <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
    @endif

    <div class="card p-4 space-y-4">
        <div>
            <label class="form-label" for="f-q">جستجو</label>
            <div class="relative">
                <input id="f-q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" class="form-input pl-10" placeholder="نام، پارت نامبر، شاسی...">
                <button class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-400 w-7 h-7" aria-label="جستجو"><i class="fa-solid fa-magnifying-glass text-xs"></i></button>
            </div>
        </div>

        @unless (isset($preset['category']))
            <div>
                <label class="form-label" for="f-category">بخش / دسته‌بندی</label>
                <select id="f-category" name="category" data-autosubmit class="form-input">
                    <option value="">همه بخش‌ها</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(($filters['category'] ?? '') === $cat->slug)>{{ $cat->name }}</option>
                        @foreach ($cat->children as $child)
                            <option value="{{ $child->slug }}" @selected(($filters['category'] ?? '') === $child->slug)>— {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        @endunless

        @unless (isset($preset['brand']))
            <div>
                <label class="form-label" for="f-brand">برند خودرو</label>
                <select id="f-brand" name="brand" data-autosubmit data-resets="model,chassis" class="form-input">
                    <option value="">همه برندها</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        @endunless

        @php($models = $selectedBrand?->carModels ?? collect())
        <div>
            <label class="form-label" for="f-model">مدل خودرو</label>
            <select id="f-model" name="model" data-autosubmit data-resets="chassis" class="form-input" @disabled($models->isEmpty())>
                <option value="">{{ $models->isEmpty() ? 'ابتدا برند را انتخاب کنید' : 'همه مدل‌ها' }}</option>
                @foreach ($models as $model)
                    <option value="{{ $model->slug }}" @selected(($filters['model'] ?? '') === $model->slug)>{{ $model->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" for="f-chassis">شاسی (اتاق)</label>
            <select id="f-chassis" name="chassis" data-autosubmit class="form-input" @disabled($chassisOptions->isEmpty() && empty($filters['chassis']))>
                <option value="">{{ $chassisOptions->isEmpty() ? 'ابتدا برند یا مدل را انتخاب کنید' : 'همه شاسی‌ها' }}</option>
                @foreach ($chassisOptions as $c)
                    <option value="{{ $c->slug }}" @selected(($filters['chassis'] ?? '') === $c->slug)>{{ $c->label() }}</option>
                @endforeach
                @if (! empty($filters['chassis']) && ! $chassisOptions->contains('slug', $filters['chassis']))
                    <option value="{{ $filters['chassis'] }}" selected>{{ $filters['chassis'] }}</option>
                @endif
            </select>
        </div>

        <div>
            <label class="form-label" for="f-year">سال ساخت خودرو</label>
            <input id="f-year" name="year" value="{{ $filters['year'] ?? '' }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input" placeholder="مثلاً 2018">
        </div>

        <div>
            <label class="form-label" for="f-condition">وضعیت کالا</label>
            <select id="f-condition" name="condition" data-autosubmit class="form-input">
                <option value="">همه</option>
                @foreach (config('shop.conditions') as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['condition'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" for="f-country">کشور سازنده</label>
            <select id="f-country" name="country" data-autosubmit class="form-input">
                <option value="">همه کشورها</option>
                @foreach (config('shop.countries') as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['country'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
            <input type="checkbox" name="in_stock" value="1" data-autosubmit class="form-check" @checked(! empty($filters['in_stock']))>
            فقط کالاهای موجود
        </label>

        <button type="submit" class="btn-primary w-full"><i class="fa-solid fa-filter"></i> اعمال فیلتر</button>
    </div>

    <div class="card p-4 text-xs text-slate-400 leading-6">
        <i class="fa-solid fa-circle-info text-sky-400 ml-1"></i>
        قطعه مورد نظرتان را پیدا نکردید؟
        <a href="{{ route('inquiries.create') }}" class="text-amber-300 font-bold">استعلام بگیرید</a>
    </div>
</form>
