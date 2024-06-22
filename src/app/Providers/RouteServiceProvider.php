<?php

namespace App\Providers;

use App\Enums\EUserRole;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Route::middleware(['api', "jwt.auth", "role:" . EUserRole::SUPERADMIN->value])
            // System

            Route::middleware(['api', "jwt.auth"])
                ->prefix('v1/system/academy')
                ->name("system-academy.")
                ->group(base_path('routes/System/academy.php'));

            // Route::middleware(['api', "jwt.auth", "role:" . EUserRole::SUPERADMIN->value])
            Route::middleware(['api', "jwt.auth"])
                ->prefix('v1/system/content')
                ->name("content.")
                ->group(base_path('routes/System/content.php'));

            Route::middleware(['api', "jwt.auth"])
                ->prefix('v0/system/content')
                ->name("v0-content.")
                ->group(base_path('routes/v0/System/content.php'));

            // Mobile
            Route::middleware(['api', "jwt.auth"])
                ->prefix('v1/mobile/hippocards')
                ->name("mobile-hippocards.")
                ->group(base_path('routes/Mobile/hippocards.php'));

            // Web

            Route::middleware('api')
                ->prefix('v1/web/academy')
                ->name("web-academy.")
                ->group(base_path('routes/Web/academy.php'));

            Route::middleware('api')
                ->prefix('v1/web/payment')
                ->name("web-payment.")
                ->group(base_path('routes/Web/payment.php'));

            // Auth

            Route::middleware('api')
                ->prefix('v1/auth')
                ->name("auth.")
                ->group(base_path('routes/auth.php'));

            // SSO

            Route::middleware('api')
                ->prefix('v1/sso')
                ->name("sso.")
                ->group(base_path('routes/sso.php'));

            // Utility

            Route::middleware('api')
                ->prefix('v1/utility')
                ->name("utility.")
                ->group(base_path('routes/utility.php'));

            // Route::middleware('web')
            //     ->group(base_path('routes/web.php'));
        });
    }
}
