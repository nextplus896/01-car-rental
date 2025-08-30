<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('system.maintenance.api','api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'verification.guard','system.maintenance'])
                ->group(base_path('routes/user.php'));

            Route::middleware(['web','auth:vendor', 'verification.guard.vendor','vendor.google.two.factor','system.maintenance'])
                ->group(base_path('routes/vendor.php'));

            Route::middleware(['web', 'auth:admin', 'app.mode', 'admin.role.guard', 'admin.google.two.factor'])
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/global.php'));

            Route::middleware('web','system.maintenance') // declare frontend routes
                ->group(base_path('routes/frontend.php'));

            Route::middleware(['api', 'auth:api','system.maintenance.api']) // User API Routes (v1)
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1/user.php'));

            Route::middleware(['api', 'auth:vendor_api','system.maintenance.api']) // Vendor API Routes (v1)
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1/vendor.php'));

            Route::middleware('api','system.maintenance.api') // Auth API Routes - User/Merchant/Agent (v1)
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1/auth.php'));

            Route::middleware(['api','system.maintenance.api']) // Global API Routes (v1)
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1/global.php'));

            $this->mapInstallerRoute();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }


    /**
     * Configure/Place installer routes.
     *
     * @return void
     */
    protected function mapInstallerRoute()
    {
        if (file_exists(base_path('resources/installer/src/routes/web.php'))) {
            Route::middleware('web')
                ->group(base_path('resources/installer/src/routes/web.php'));
        }
    }
}
