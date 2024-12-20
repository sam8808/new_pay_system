<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
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
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')->group(function () {
                require base_path('routes/web.php');
                require base_path('routes/account.php');
            });

            Route::middleware('web')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api.php');
                });

            Route::middleware('web')
                ->prefix('admin')
                ->group(function () {
                    require base_path('routes/admin.php');
                });
        });
    }
}
