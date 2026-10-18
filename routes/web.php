<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category}', [ProductController::class, 'category'])->name('categories.show');
Route::get('/brand/{brand}', [ProductController::class, 'brand'])->name('brands.show');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->middleware('throttle:search')->name('search.suggest');

Route::controller(PageController::class)->group(function () {
    Route::get('/about', 'about')->name('pages.about');
    Route::get('/contact', 'contact')->name('pages.contact');
    Route::get('/faq', 'faq')->name('pages.faq');
    Route::get('/how-to-buy', 'howToBuy')->name('pages.how-to-buy');
    Route::get('/terms', 'terms')->name('pages.terms');
});

Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/items/{product}', 'add')->name('add');
    Route::patch('/items/{product}', 'update')->name('update');
    Route::delete('/items/{product}', 'remove')->name('remove');
});

/*
|--------------------------------------------------------------------------
| Authentication (login / OTP registration / password reset)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::controller(RegisterController::class)->prefix('register')->name('register.')->group(function () {
        Route::post('/', 'sendCode')->middleware('throttle:otp-send')->name('send');
        Route::get('/verify', 'showVerify')->name('verify');
        Route::post('/verify', 'verify')->middleware('throttle:otp-verify')->name('verify.check');
        Route::post('/resend', 'resend')->middleware('throttle:otp-send')->name('resend');
        Route::get('/complete', 'showComplete')->name('complete');
        Route::post('/complete', 'complete')->name('store');
    });

    Route::controller(PasswordResetController::class)->prefix('forgot-password')->name('password.')->group(function () {
        Route::get('/', 'show')->name('request');
        Route::post('/', 'sendCode')->middleware('throttle:otp-send')->name('send');
        Route::get('/reset', 'showReset')->name('reset');
        Route::post('/reset', 'reset')->middleware('throttle:otp-verify')->name('update');
    });
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Signed-in customers
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/inquiry', [InquiryController::class, 'create'])->name('inquiries.create');
    Route::post('/inquiry', [InquiryController::class, 'store'])->middleware('throttle:inquiries')->name('inquiries.store');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', Account\DashboardController::class)->name('dashboard');

        Route::get('/profile', [Account\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [Account\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [Account\ProfileController::class, 'updatePassword'])->name('password.update');

        Route::controller(Account\ContactController::class)->prefix('contact')->name('contact.')->group(function () {
            Route::get('/{channel}', 'edit')->whereIn('channel', ['email', 'phone'])->name('edit');
            Route::post('/{channel}', 'sendCode')->whereIn('channel', ['email', 'phone'])->middleware('throttle:otp-send')->name('send');
            Route::get('/{channel}/verify', 'showVerify')->whereIn('channel', ['email', 'phone'])->name('verify');
            Route::post('/{channel}/verify', 'verify')->whereIn('channel', ['email', 'phone'])->middleware('throttle:otp-verify')->name('verify.check');
        });

        Route::get('/inquiries', [Account\InquiryController::class, 'index'])->name('inquiries');
        Route::get('/inquiries/{inquiry}', [Account\InquiryController::class, 'show'])->name('inquiries.show');
        Route::get('/orders', [Account\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [Account\OrderController::class, 'show'])->name('orders.show');
    });
});
