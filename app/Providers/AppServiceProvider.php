<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share app settings with all Inertia pages
        Inertia::share([
            'appSettings' => function () {
                $settings = AppSetting::first();
                return $settings ? [
                    'app_name' => AppSetting::get('app_name', 'Report App'),
                    'logo' => AppSetting::get('logo', ''),
                    'favicon' => AppSetting::get('favicon', ''),
                ] : [
                    'app_name' => 'Report App',
                    'logo' => '',
                    'favicon' => '',
                ];
            }
        ]);
    }
}
