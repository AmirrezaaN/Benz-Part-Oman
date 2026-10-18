<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarModelController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.car-models.index', [
            'models' => CarModel::query()->with('brand:id,name')->withCount('chassis')
                ->when($request->query('brand'), fn ($q, $v) => $q->where('brand_id', $v))
                ->orderBy('brand_id')->orderBy('sort_order')->orderBy('name')->paginate(30)->withQueryString(),
            'brands' => Brand::query()->ordered()->get(['id', 'name']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.car-models.form', [
            'model' => new CarModel(['brand_id' => $request->integer('brand') ?: null]),
            'brands' => Brand::query()->ordered()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $model = CarModel::query()->create($this->validated($request));
        ActivityLog::record('car_model.created', $model, 'ساخت مدل «'.$model->name.'»');

        return redirect()->route('admin.car-models.index', ['brand' => $model->brand_id])->with('success', 'مدل خودرو ساخته شد.');
    }

    public function edit(CarModel $carModel): View
    {
        return view('admin.car-models.form', ['model' => $carModel, 'brands' => Brand::query()->ordered()->get(['id', 'name'])]);
    }

    public function update(Request $request, CarModel $carModel): RedirectResponse
    {
        $carModel->update($this->validated($request));
        ActivityLog::record('car_model.updated', $carModel, 'ویرایش مدل «'.$carModel->name.'»');

        return redirect()->route('admin.car-models.index', ['brand' => $carModel->brand_id])->with('success', 'مدل خودرو ویرایش شد.');
    }

    public function destroy(CarModel $carModel): RedirectResponse
    {
        $carModel->delete();
        ActivityLog::record('car_model.deleted', null, 'حذف مدل «'.$carModel->name.'»');

        return back()->with('success', 'مدل و شاسی‌های آن حذف شد.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:80'],
            'name_fa' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [], ['brand_id' => 'برند', 'name' => 'نام مدل', 'name_fa' => 'نام فارسی', 'sort_order' => 'ترتیب']);

        return ['sort_order' => $data['sort_order'] ?? 0] + $data;
    }
}
