<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use App\Support\Identifier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route middleware already checks products.create / products.edit
    }

    protected function prepareForValidation(): void
    {
        $numeric = fn ($v) => $v === null || $v === '' ? null : str_replace([',', '،', ' '], '', Identifier::normalizeDigits((string) $v));

        $this->merge([
            'quantity' => $numeric($this->input('quantity')),
            'price_omr' => $numeric($this->input('price_omr')),
            'price_toman' => $numeric($this->input('price_toman')),
            'year_from' => $numeric($this->input('year_from')),
            'year_to' => $numeric($this->input('year_to')),
            'part_number' => $this->filled('part_number') ? Product::normalizePartNumber($this->input('part_number')) : null,
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        $maxYear = now()->year + 1;

        return [
            'name' => ['required', 'string', 'max:255'],
            'part_number' => ['nullable', 'string', 'max:60'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'manufacturer' => ['nullable', 'string', 'max:80'],
            'country_of_origin' => ['nullable', Rule::in(array_keys(config('shop.countries')))],
            'year_from' => ['nullable', 'integer', 'min:1950', 'max:'.$maxYear],
            'year_to' => ['nullable', 'integer', 'min:1950', 'max:'.$maxYear, 'gte:year_from'],
            'condition' => ['nullable', Rule::in(array_keys(config('shop.conditions')))],
            'quantity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'price_omr' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'price_toman' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'warehouse' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:10000'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'chassis' => ['nullable', 'array'],
            'chassis.*' => ['integer', 'exists:chassis,id'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام محصول', 'part_number' => 'پارت نامبر', 'brand_id' => 'برند', 'category_id' => 'بخش',
            'manufacturer' => 'سازنده قطعه', 'country_of_origin' => 'کشور ساخت', 'year_from' => 'سال ساخت از',
            'year_to' => 'سال ساخت تا', 'condition' => 'وضعیت کالا', 'quantity' => 'تعداد', 'price_omr' => 'قیمت (ریال عمان)',
            'price_toman' => 'قیمت تومان', 'warehouse' => 'انبار', 'description' => 'توضیحات', 'chassis' => 'شاسی‌ها',
            'images' => 'تصاویر', 'images.*' => 'تصویر',
        ];
    }
}
