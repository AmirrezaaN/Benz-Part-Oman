<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Chassis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChassisController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.chassis.index', [
            'items' => Chassis::query()->with('carModel.brand:id,name')->withCount('products')
                ->when($request->query('model'), fn ($q, $v) => $q->where('car_model_id', $v))
                ->when($request->query('q'), fn ($q, $v) => $q->where('code', 'like', '%'.$v.'%'))
                ->orderBy('car_model_id')->orderBy('sort_order')->orderBy('code')->paginate(30)->withQueryString(),
            'brands' => $this->brandTree(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.chassis.form', [
            'item' => new Chassis(['car_model_id' => $request->integer('model') ?: null]),
            'brands' => $this->brandTree(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $chassis = new Chassis($this->validated($request));
        $chassis->save();
        ActivityLog::record('chassis.created', $chassis, 'ساخت شاسی «'.$chassis->code.'»');

        return redirect()->route('admin.chassis.index', ['model' => $chassis->car_model_id])->with('success', 'شاسی ساخته شد.');
    }

    public function edit(Chassis $chassis): View
    {
        return view('admin.chassis.form', ['item' => $chassis, 'brands' => $this->brandTree()]);
    }

    public function update(Request $request, Chassis $chassis): RedirectResponse
    {
        $chassis->update($this->validated($request));
        ActivityLog::record('chassis.updated', $chassis, 'ویرایش شاسی «'.$chassis->code.'»');

        return redirect()->route('admin.chassis.index', ['model' => $chassis->car_model_id])->with('success', 'شاسی ویرایش شد.');
    }

    public function destroy(Chassis $chassis): RedirectResponse
    {
        $chassis->delete();
        ActivityLog::record('chassis.deleted', null, 'حذف شاسی «'.$chassis->code.'»');

        return back()->with('success', 'شاسی حذف شد.');
    }

    private function validated(Request $request): array
    {
        $maxYear = now()->year + 2;
        $data = $request->validate([
            'car_model_id' => ['required', 'integer', 'exists:car_models,id'],
            'code' => ['required', 'string', 'max:30'],
            'title' => ['nullable', 'string', 'max:100'],
            'year_from' => ['nullable', 'integer', 'min:1950', 'max:'.$maxYear],
            'year_to' => ['nullable', 'integer', 'min:1950', 'max:'.$maxYear, 'gte:year_from'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [], [
            'car_model_id' => 'مدل خودرو', 'code' => 'کد شاسی', 'title' => 'عنوان', 'year_from' => 'از سال',
            'year_to' => 'تا سال', 'sort_order' => 'ترتیب',
        ]);

        $data['code'] = strtoupper(trim($data['code']));

        return ['sort_order' => $data['sort_order'] ?? 0] + $data;
    }

    private function brandTree()
    {
        return Brand::query()->ordered()->with('carModels:id,brand_id,name')->get(['id', 'name']);
    }
}
