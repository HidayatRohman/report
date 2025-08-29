<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        // Fix for older MySQL versions - set default string length
        Schema::defaultStringLength(191);
        
        // Share app settings with all Inertia pages
        Inertia::share([
            'appSettings' => function () {
                return [
                    'app_name' => AppSetting::get('app_name', 'Kelola Pusat App'),
                    'logo' => AppSetting::get('logo'),
                    'favicon' => AppSetting::get('favicon'),
                ];
            }
        ]);
    }
}
