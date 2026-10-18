<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
| Every route requires a signed-in user holding the "admin.access" permission;
| each section additionally checks its own permission.
*/
Route::prefix(config('shop.admin_path'))
    ->name('admin.')
    ->middleware(['auth', 'can:admin.access'])
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::get('/stats', [Admin\StatsController::class, 'index'])->middleware('can:stats.view')->name('stats');

        // Products
        Route::get('/products', [Admin\ProductController::class, 'index'])->middleware('can:products.view')->name('products.index');
        Route::get('/products/create', [Admin\ProductController::class, 'create'])->middleware('can:products.create')->name('products.create');
        Route::post('/products', [Admin\ProductController::class, 'store'])->middleware('can:products.create')->name('products.store');
        Route::get('/products/{product:id}/edit', [Admin\ProductController::class, 'edit'])->middleware('can:products.edit')->name('products.edit');
        Route::put('/products/{product:id}', [Admin\ProductController::class, 'update'])->middleware('can:products.edit')->name('products.update');
        Route::delete('/products/{product:id}', [Admin\ProductController::class, 'destroy'])->middleware('can:products.delete')->name('products.destroy');
        Route::delete('/products/{product:id}/images/{image}', [Admin\ProductController::class, 'destroyImage'])->middleware('can:products.edit')->scopeBindings()->name('products.images.destroy');

        // Catalog structure
        Route::middleware('can:catalog.manage')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->except('show')->parameters(['categories' => 'category:id']);
            Route::resource('brands', Admin\BrandController::class)->except('show')->parameters(['brands' => 'brand:id']);
            Route::resource('car-models', Admin\CarModelController::class)->except('show')->parameters(['car-models' => 'carModel']);
            Route::resource('chassis', Admin\ChassisController::class)->except('show')->parameters(['chassis' => 'chassis']);
        });

        // Inquiries
        Route::get('/inquiries', [Admin\InquiryController::class, 'index'])->middleware('can:inquiries.view')->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [Admin\InquiryController::class, 'show'])->middleware('can:inquiries.view')->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [Admin\InquiryController::class, 'update'])->middleware('can:inquiries.reply')->name('inquiries.update');

        // Orders
        Route::get('/orders', [Admin\OrderController::class, 'index'])->middleware('can:orders.view')->name('orders.index');
        Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->middleware('can:orders.view')->name('orders.show');
        Route::put('/orders/{order}', [Admin\OrderController::class, 'update'])->middleware('can:orders.manage')->name('orders.update');

        // Users (resolved by their internal code, never the sequential id)
        Route::get('/users', [Admin\UserController::class, 'index'])->middleware('can:users.view')->name('users.index');
        Route::get('/users/{user}', [Admin\UserController::class, 'show'])->middleware('can:users.view')->name('users.show');
        Route::put('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->middleware('can:users.change-role')->name('users.role');
        Route::put('/users/{user}/ban', [Admin\UserController::class, 'toggleBan'])->middleware('can:users.ban')->name('users.ban');

        // Super admin only
        Route::middleware('can:super-admin')->group(function () {
            Route::resource('roles', Admin\RoleController::class)->except('show');
            Route::get('/activity', [Admin\ActivityLogController::class, 'index'])->name('activity.index');
        });

        Route::resource('banners', Admin\BannerController::class)->except('show')->middleware('can:banners.manage');

        Route::get('/settings', [Admin\SettingController::class, 'edit'])->middleware('can:settings.manage')->name('settings.edit');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->middleware('can:settings.manage')->name('settings.update');
    });
