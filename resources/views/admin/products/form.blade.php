@extends('admin.layout')

@section('title', $product->exists ? 'ویرایش محصول' : 'ساخت محصول جدید')

@section('content')
    @php($err = fn ($f) => $errors->has($f) ? 'border-rose-500!' : '')

    <div class="bg-sky-500/5 border border-sky-500/20 rounded-xl p-3 mb-5 text-[11px] text-slate-300 leading-6">
        <i class="fa-solid fa-circle-info text-sky-400 ml-1"></i>
        فقط «نام محصول» الزامی است. هر فیلدی که خالی بماند یعنی آن محصول آن اطلاعات را ندارد و در سایت نمایش داده نمی‌شود.
        محصولِ بدون قیمت یا بدون موجودی فقط قابل «استعلام» است و به سبد خرید اضافه نمی‌شود.
    </div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
          class="grid xl:grid-cols-3 gap-5">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="xl:col-span-2 space-y-5 min-w-0">
            {{-- Basic info --}}
            <section class="card p-5">
                <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-circle-info text-sky-400 ml-1.5"></i>اطلاعات اصلی</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">نام محصول <span class="text-rose-400">*</span></label>
                        <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255" class="form-input {{ $err('name') }}" placeholder="مثلاً: کمک فنر بادی جلو مرسدس بنز S-Class">
                    </div>
                    <div>
                        <label class="form-label" for="part_number">پارت نامبر (OEM)</label>
                        <input id="part_number" name="part_number" value="{{ old('part_number', $product->part_number) }}" maxlength="60" dir="ltr" data-latin-digits class="form-input font-mono text-left {{ $err('part_number') }}" placeholder="A2223204713">
                        <p class="form-hint">فاصله و خط تیره خودکار حذف می‌شود.</p>
                    </div>
                    <div>
                        <label class="form-label" for="category_id">بخش (موتور، بدنه، جلوبندی ...)</label>
                        <select id="category_id" name="category_id" class="form-input {{ $err('category_id') }}">
                            <option value="">— بدون بخش —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id) == $child->id)>— {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="brand_id">برند خودرو</label>
                        <select id="brand_id" name="brand_id" class="form-input {{ $err('brand_id') }}">
                            <option value="">— بدون برند —</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}{{ $b->name_fa ? ' - '.$b->name_fa : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="manufacturer">سازنده قطعه</label>
                        <input id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}" maxlength="80" class="form-input" placeholder="Bosch، Mercedes-Benz Genuine ...">
                    </div>
                    <div>
                        <label class="form-label" for="country_of_origin">کشور ساخت</label>
                        <select id="country_of_origin" name="country_of_origin" class="form-input {{ $err('country_of_origin') }}">
                            <option value="">— نامشخص —</option>
                            @foreach (config('shop.countries') as $key => $label)
                                <option value="{{ $key }}" @selected(old('country_of_origin', $product->country_of_origin) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="condition">وضعیت کالا</label>
                        <select id="condition" name="condition" class="form-input {{ $err('condition') }}">
                            <option value="">— نامشخص —</option>
                            @foreach (config('shop.conditions') as $key => $label)
                                <option value="{{ $key }}" @selected(old('condition', $product->condition) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label" for="year_from">سال ساخت از</label>
                            <input id="year_from" name="year_from" value="{{ old('year_from', $product->year_from) }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input {{ $err('year_from') }}" placeholder="2014">
                        </div>
                        <div>
                            <label class="form-label" for="year_to">تا سال</label>
                            <input id="year_to" name="year_to" value="{{ old('year_to', $product->year_to) }}" inputmode="numeric" maxlength="4" data-latin-digits class="form-input {{ $err('year_to') }}" placeholder="2020">
                        </div>
                    </div>
                </div>
            </section>

            {{-- Compatible chassis --}}
            <section class="card p-5" data-chassis-picker>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <h2 class="text-sm font-bold text-white"><i class="fa-solid fa-car text-sky-400 ml-1.5"></i>شاسی‌هایی که این قطعه به آن‌ها می‌خورد
                        <span class="text-sky-400">(<span data-chassis-count>0</span>)</span></h2>
                    <input type="search" data-chassis-filter placeholder="فیلتر: W213، E-Class، Toyota ..." class="form-input py-2! text-xs max-w-60">
                </div>
                <div class="max-h-96 overflow-y-auto space-y-4 pl-1">
                    @foreach ($chassisTree as $brand)
                        @if ($brand->carModels->isNotEmpty())
                            <div>
                                <div class="text-xs font-black text-slate-400 mb-2">{{ $brand->name }}</div>
                                <div class="grid sm:grid-cols-2 gap-2">
                                    @foreach ($brand->carModels as $model)
                                        @if ($model->chassis->isNotEmpty())
                                            <div data-chassis-group data-search="{{ strtolower($brand->name.' '.$model->name) }}" class="bg-slate-950/60 border border-slate-800 rounded-xl p-3">
                                                <label class="flex items-center gap-2 text-xs font-bold text-white mb-2 cursor-pointer">
                                                    <input type="checkbox" data-select-model class="form-check"> {{ $model->name }}
                                                </label>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($model->chassis as $c)
                                                        <label data-chassis-item data-search="{{ strtolower($c->code) }}"
                                                               class="flex items-center gap-1.5 text-[11px] bg-slate-900 border border-slate-700 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-500/10 has-[:checked]:text-sky-300 rounded-lg px-2 py-1 cursor-pointer font-mono" dir="ltr">
                                                            <input type="checkbox" name="chassis[]" value="{{ $c->id }}" class="form-check w-3! h-3!" @checked(in_array($c->id, $selectedChassis))>
                                                            {{ $c->label() }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @can('catalog.manage')
                    <p class="form-hint mt-3">شاسی مورد نظر نیست؟ از <a href="{{ route('admin.chassis.create') }}" target="_blank" class="text-sky-400">بخش شاسی‌ها</a> اضافه کنید.</p>
                @endcan
            </section>

            {{-- Images --}}
            <section class="card p-5">
                <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-images text-sky-400 ml-1.5"></i>تصاویر محصول <span class="text-[11px] text-slate-500 font-normal">(حداکثر ۸ تصویر، هر کدام تا ۵ مگابایت — به WebP تبدیل می‌شوند)</span></h2>
                @if ($product->exists && $product->images->isNotEmpty())
                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach ($product->images as $i => $image)
                            <div class="relative w-24">
                                <img src="{{ $image->url() }}" alt="" class="w-24 h-24 object-cover rounded-xl border border-slate-700">
                                <label class="flex items-center gap-1 text-[10px] text-slate-400 mt-1 cursor-pointer">
                                    <input type="radio" name="main_image" value="{{ $image->id }}" class="accent-sky-500" @checked($i === 0)> تصویر اصلی
                                </label>
                                <button type="submit" form="delete-image-{{ $image->id }}" class="absolute top-1 left-1 w-6 h-6 rounded-md bg-rose-600/90 text-white text-[10px]" title="حذف تصویر"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" data-preview="img-preview"
                       class="form-input file:ml-3 file:rounded-lg file:border-0 file:bg-sky-500 file:text-slate-950 file:font-bold file:px-3 file:py-1 file:text-xs">
                <div id="img-preview" class="flex flex-wrap gap-2 mt-3"></div>
            </section>

            {{-- Description --}}
            <section class="card p-5">
                <label class="form-label text-sm! mb-3!" for="description"><i class="fa-solid fa-align-right text-sky-400 ml-1.5"></i>توضیحات</label>
                <textarea id="description" name="description" rows="6" maxlength="10000" class="form-input" placeholder="شرایط کالا، محتویات بسته، نکات نصب ...">{{ old('description', $product->description) }}</textarea>
            </section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <section class="card p-5">
                <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-coins text-sky-400 ml-1.5"></i>قیمت و موجودی</h2>
                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="price_omr">قیمت (ریال عمان - OMR)</label>
                        <input id="price_omr" name="price_omr" value="{{ old('price_omr', $product->price_omr !== null ? format_omr($product->price_omr) : '') }}" inputmode="decimal" dir="ltr" data-latin-digits
                               data-omr-input="toman-preview" data-rate="{{ exchange_rate() }}" class="form-input text-left {{ $err('price_omr') }}" placeholder="395.500">
                        <p id="toman-preview" class="text-[11px] text-emerald-400 mt-1"></p>
                        <p class="form-hint">نرخ فعلی: هر ریال عمان = {{ format_toman(exchange_rate()) }} تومان</p>
                    </div>
                    <div>
                        <label class="form-label" for="price_toman">قیمت تومانی دستی (اختیاری)</label>
                        <input id="price_toman" name="price_toman" value="{{ old('price_toman', $product->price_toman) }}" inputmode="numeric" dir="ltr" data-latin-digits class="form-input text-left {{ $err('price_toman') }}" placeholder="خالی = محاسبه خودکار">
                        <p class="form-hint">اگر پر شود، به‌جای محاسبه از نرخ ارز نمایش داده می‌شود.</p>
                    </div>
                    <div>
                        <label class="form-label" for="quantity">تعداد موجودی</label>
                        <input id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}" inputmode="numeric" dir="ltr" data-latin-digits class="form-input text-left {{ $err('quantity') }}" placeholder="خالی = موجودی با استعلام">
                    </div>
                    <div>
                        <label class="form-label" for="warehouse">انبار</label>
                        <input id="warehouse" name="warehouse" value="{{ old('warehouse', $product->warehouse) }}" maxlength="100" class="form-input" placeholder="انبار معبیله مسقط">
                    </div>
                </div>
            </section>

            <section class="card p-5 space-y-3">
                <label class="flex items-center justify-between gap-3 text-xs text-slate-200 cursor-pointer">
                    <span><i class="fa-solid fa-eye text-emerald-400 ml-1.5"></i>نمایش در سایت (فعال)</span>
                    <input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $product->is_active))>
                </label>
                <label class="flex items-center justify-between gap-3 text-xs text-slate-200 cursor-pointer">
                    <span><i class="fa-solid fa-star text-amber-300 ml-1.5"></i>پیشنهاد ویژه (اسلایدر صفحه اصلی)</span>
                    <input type="checkbox" name="is_featured" value="1" class="form-check" @checked(old('is_featured', $product->is_featured))>
                </label>
            </section>

            <div class="card p-5 space-y-2 xl:sticky xl:top-20">
                <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-floppy-disk"></i> {{ $product->exists ? 'ذخیره تغییرات' : 'ساخت محصول' }}</button>
                @if ($product->exists)
                    <a href="{{ route('products.show', $product) }}" target="_blank" class="btn-ghost w-full"><i class="fa-solid fa-eye"></i> مشاهده در سایت</a>
                    <p class="text-[10px] text-slate-500 text-center pt-1">ساخته شده {{ jdate($product->created_at, 'Y/m/d H:i') }} · {{ fa_num($product->views) }} بازدید</p>
                @endif
                <a href="{{ route('admin.products.index') }}" class="btn-ghost w-full">بازگشت به لیست</a>
            </div>
        </div>
    </form>

    @if ($product->exists)
        @foreach ($product->images as $image)
            <form id="delete-image-{{ $image->id }}" method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" data-confirm="این تصویر حذف شود؟" hidden>
                @csrf @method('DELETE')
            </form>
        @endforeach
        @can('products.delete')
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="محصول «{{ $product->name }}» حذف شود؟" class="mt-5">
                @csrf @method('DELETE')
                <button class="btn-danger"><i class="fa-solid fa-trash-can"></i> حذف محصول</button>
            </form>
        @endcan
    @endif
@endsection
