<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        try {
            if (Schema::hasTable('app_settings')) {
                View::share('appBranding', AppSetting::getBranding());
            } else {
                View::share('appBranding', [
                    'app_name' => 'Sekolahku',
                    'app_tagline' => 'v2.0',
                    'app_logo' => null,
                ]);
            }
        } catch (\Throwable) {
            View::share('appBranding', [
                'app_name' => 'Sekolahku',
                'app_tagline' => 'v2.0',
                'app_logo' => null,
            ]);
        }
    }
}
