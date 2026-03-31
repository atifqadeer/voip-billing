<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
    \Laravel\Fortify\Contracts\RegisterViewResponse::class,
    
);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Retrieve settings as key-value pairs (param as the key, value as the value)
        $settings = Setting::pluck('value', 'param')->toArray();

        // Share the settings globally in views
        view()->share('settings', $settings);
    }
}
