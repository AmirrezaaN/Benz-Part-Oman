<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Inquiry;
use App\Models\Product;
use App\Services\ImageService;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function create(Request $request): View
    {
        $product = $request->filled('product')
            ? Product::query()->active()->where('slug', $request->query('product'))->first()
            : null;

        return view('inquiries.create', [
            'product' => $product,
            'brands' => Brand::query()->active()->ordered()->get(['id', 'name', 'name_fa']),
        ]);
    }

    public function store(Request $request, ImageService $images): RedirectResponse
    {
        $request->merge([
            'vin' => strtoupper(preg_replace('/\s+/', '', Identifier::normalizeDigits((string) $request->input('vin')))) ?: null,
            'part_number' => $request->filled('part_number') ? Product::normalizePartNumber($request->input('part_number')) : null,
            'year' => $request->filled('year') ? Identifier::normalizeDigits((string) $request->input('year')) : null,
            'quantity' => $request->filled('quantity') ? Identifier::normalizeDigits((string) $request->input('quantity')) : 1,
        ]);

        $data = $request->validate([
            'part_name' => ['required', 'string', 'max:150'],
            'vin' => ['nullable', 'string', 'size:17', 'regex:/^[A-HJ-NPR-Z0-9]{17}$/'],
            'part_number' => ['nullable', 'string', 'max:60'],
            'brand_id' => ['nullable', Rule::exists('brands', 'id')->where('is_active', true)],
            'car_model' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'message' => ['nullable', 'string', 'max:2000'],
            'product_id' => ['nullable', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], ['vin.regex' => 'شماره شاسی (VIN) باید ۱۷ کاراکتر انگلیسی/عدد باشد (بدون حروف I، O و Q).'], [
            'part_name' => 'نام قطعه', 'vin' => 'شماره شاسی (VIN)', 'part_number' => 'پارت نامبر',
            'brand_id' => 'برند خودرو', 'car_model' => 'مدل خودرو', 'year' => 'سال ساخت',
            'quantity' => 'تعداد', 'message' => 'توضیحات', 'image' => 'تصویر قطعه',
        ]);

        $inquiry = new Inquiry(collect($data)->except('image')->map(fn ($v) => is_string($v) ? strip_tags($v) : $v)->all());
        $inquiry->user_id = $request->user()->id;
        if ($request->hasFile('image')) {
            $inquiry->image = $images->store($request->file('image'), 'inquiries');
        }
        $inquiry->save();

        return redirect()->route('account.inquiries.show', $inquiry)
            ->with('success', 'استعلام شما با کد '.$inquiry->code.' ثبت شد. پاسخ کارشناسان در همین صفحه نمایش داده می‌شود.');
    }
}
