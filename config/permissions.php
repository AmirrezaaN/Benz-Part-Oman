<?php

/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
| The single source of truth for every permission in the site. Roles store a
| list of these keys. The "super-admin" role bypasses every check.
| Managing roles and viewing the activity log are reserved for the super admin.
*/

return [
    'groups' => [
        'پنل مدیریت' => [
            'admin.access' => 'ورود به پنل مدیریت',
            'stats.view' => 'مشاهده آمار سایت',
        ],
        'محصولات' => [
            'products.view' => 'مشاهده لیست محصولات',
            'products.create' => 'ساخت محصول جدید',
            'products.edit' => 'ویرایش محصولات',
            'products.delete' => 'حذف محصولات',
            'catalog.manage' => 'مدیریت دسته‌بندی‌ها، برندها، مدل‌ها و شاسی‌ها',
        ],
        'استعلام و سفارش' => [
            'inquiries.view' => 'مشاهده استعلام‌ها',
            'inquiries.reply' => 'پاسخ به استعلام‌ها',
            'orders.view' => 'مشاهده سفارش‌ها',
            'orders.manage' => 'تغییر وضعیت سفارش‌ها',
        ],
        'کاربران' => [
            'users.view' => 'مشاهده کاربران',
            'users.change-role' => 'تغییر نقش کاربران',
            'users.ban' => 'مسدود / رفع مسدودی کاربران',
        ],
        'تنظیمات' => [
            'banners.manage' => 'مدیریت اسلایدرهای صفحه اصلی',
            'settings.manage' => 'تنظیمات سایت و نرخ ارز',
        ],
    ],
];
