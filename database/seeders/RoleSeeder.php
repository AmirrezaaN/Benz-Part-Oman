<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $all = array_keys(Role::allPermissions());

        $roles = [
            Role::SUPER_ADMIN => ['مدیر کل', 'مالک سایت — همه دسترسی‌ها، مدیریت نقش‌ها و گزارش فعالیت‌ها', []],
            Role::ADMIN => ['مدیر', 'دسترسی کامل به پنل (به جز مدیریت نقش‌ها)', $all],
            'sales' => ['کارشناس فروش', 'مدیریت محصولات، استعلام‌ها و سفارش‌ها', [
                'admin.access', 'products.view', 'products.create', 'products.edit',
                'inquiries.view', 'inquiries.reply', 'orders.view', 'orders.manage',
            ]],
            Role::CUSTOMER => ['مشتری', 'نقش پیش‌فرض همه کاربران', []],
        ];

        foreach ($roles as $name => [$label, $description, $permissions]) {
            $role = Role::query()->firstOrNew(['name' => $name]);
            $role->fill(['label' => $label, 'description' => $description]);
            if (! $role->exists) {
                $role->permissions = $permissions;
            }
            $role->is_system = in_array($name, [Role::SUPER_ADMIN, Role::ADMIN, Role::CUSTOMER], true);
            $role->save();
        }
    }
}
