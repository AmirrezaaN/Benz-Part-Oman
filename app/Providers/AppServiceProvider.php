<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Chassis;
use App\Models\Role;
use App\Models\User;
use App\Services\Cart;
use App\Services\Sms\KavenegarSmsSender;
use App\Services\Sms\LogSmsSender;
use App\Support\Identifier;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Cart::class);

        $this->app->bind(SmsSender::class, fn () => match (config('shop.sms.driver')) {
            'kavenegar' => new KavenegarSmsSender((string) config('shop.sms.api_key'), (string) config('shop.sms.sender')),
            default => new LogSmsSender,
        });
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        Paginator::defaultView('partials.pagination');

        $this->registerGates();
        $this->registerRateLimiters();
        $this->registerViewComposers();
    }

    private function registerGates(): void
    {
        // The owner (super-admin) passes every check.
        Gate::before(fn (User $user) => $user->isSuperAdmin() && ! $user->isBanned() ? true : null);

        foreach (array_keys(Role::allPermissions()) as $permission) {
            Gate::define($permission, fn (User $user) => ! $user->isBanned() && $user->hasPermission($permission));
        }

        // Reserved for the super admin only (Gate::before grants it).
        Gate::define('super-admin', fn () => false);
    }

    private function registerRateLimiters(): void
    {
        $identifier = fn (Request $r) => Identifier::parse($r->input('identifier') ?? $r->input('value'))['value'] ?? '';

        // Sending verification codes costs money (SMS) and can be abused for spam.
        RateLimiter::for('otp-send', fn (Request $r) => [
            Limit::perMinutes(10, 5)->by('otp-ip:'.$r->ip()),
            Limit::perHour(8)->by('otp-id:'.$identifier($r)),
        ]);

        RateLimiter::for('otp-verify', fn (Request $r) => Limit::perMinute(10)->by('otp-verify:'.$r->ip()));

        RateLimiter::for('inquiries', fn (Request $r) => [
            Limit::perMinutes(10, 5)->by('inq:'.($r->user()?->id ?: $r->ip())),
            Limit::perDay(30)->by('inq-day:'.($r->user()?->id ?: $r->ip())),
        ]);

        RateLimiter::for('checkout', fn (Request $r) => Limit::perMinutes(10, 5)->by('checkout:'.($r->user()?->id ?: $r->ip())));

        RateLimiter::for('search', fn (Request $r) => Limit::perMinute(60)->by('search:'.$r->ip()));
    }

    private function registerViewComposers(): void
    {
        // Navigation caches are dropped whenever the catalog structure changes
        foreach ([Brand::class, CarModel::class, Chassis::class, Category::class] as $model) {
            $model::saved(fn () => Cache::deleteMultiple(['nav_categories', 'nav_chassis', 'home_stats']));
            $model::deleted(fn () => Cache::deleteMultiple(['nav_categories', 'nav_chassis', 'home_stats']));
        }

        View::composer(['layouts.app', 'layouts.auth'], function ($view) {
            $view->with('navCategories', Cache::remember('nav_categories', 600, fn () => Category::query()
                ->active()->whereNull('parent_id')->ordered()->get(['id', 'name', 'slug', 'icon'])));
            // Quick chassis picker in the header (featured brands only)
            $view->with('navChassis', Cache::remember('nav_chassis', 600, fn () => Brand::query()
                ->active()->where('is_featured', true)->ordered()
                ->with('carModels.chassis:id,car_model_id,code,slug')->get(['id', 'name'])
                ->map(fn (Brand $b) => [
                    'brand' => $b->name,
                    'items' => $b->carModels->flatMap(fn ($m) => $m->chassis->map(fn ($c) => ['slug' => $c->slug, 'label' => $m->name.' '.$c->code])),
                ])->filter(fn ($g) => $g['items']->isNotEmpty())->values()));
            $view->with('cartCount', app(Cart::class)->count());
            $view->with('currency', request()->cookie('currency') === 'omr' ? 'omr' : 'toman');
        });
    }
}
