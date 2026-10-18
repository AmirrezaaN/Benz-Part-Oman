<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Roles & permissions — super admin only (enforced by route middleware). */
class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', ['roles' => Role::query()->withCount('users')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => new Role(['permissions' => ['admin.access']])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $role = new Role($data);
        $role->name = $this->uniqueName($request->input('name') ?: $data['label']);
        $role->save();
        ActivityLog::record('role.created', $role, 'ساخت نقش «'.$role->label.'»');

        return redirect()->route('admin.roles.index')->with('success', 'نقش جدید ساخته شد.');
    }

    public function edit(Role $role): View|RedirectResponse
    {
        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')->with('error', 'نقش مدیر کل قابل ویرایش نیست و همه دسترسی‌ها را دارد.');
        }

        return view('admin.roles.form', ['role' => $role]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->isSuperAdmin(), 403);

        $data = $this->validated($request);
        if ($role->name === Role::CUSTOMER) {
            $data['permissions'] = []; // customers never get panel permissions
        }
        $role->update($data);

        // Active sessions of this role's users are dropped so new permissions apply immediately
        DB::table('sessions')->whereIn('user_id', $role->users()->pluck('id'))->delete();
        ActivityLog::record('role.updated', $role, 'ویرایش دسترسی‌های نقش «'.$role->label.'»');

        return redirect()->route('admin.roles.index')->with('success', 'نقش به‌روزرسانی شد.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'نقش‌های سیستمی قابل حذف نیستند.');
        }

        // Users of a removed role fall back to "customer"
        $role->users()->update(['role_id' => Role::customer()->id]);
        $role->delete();
        ActivityLog::record('role.deleted', null, 'حذف نقش «'.$role->label.'»');

        return back()->with('success', 'نقش حذف شد و کاربران آن به نقش مشتری منتقل شدند.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:50', 'regex:/^[a-z][a-z0-9-]*$/'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Role::allPermissions()))],
        ], ['name.regex' => 'شناسه نقش فقط حروف کوچک انگلیسی، عدد و خط تیره.'], [
            'label' => 'نام نقش', 'name' => 'شناسه', 'description' => 'توضیحات', 'permissions' => 'دسترسی‌ها',
        ]);

        return [
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
        ];
    }

    private function uniqueName(string $value): string
    {
        $base = Str::slug($value) ?: 'role';
        $name = $base;
        $i = 2;
        while (Role::query()->where('name', $name)->exists()) {
            $name = $base.'-'.$i++;
        }

        return $name;
    }
}
