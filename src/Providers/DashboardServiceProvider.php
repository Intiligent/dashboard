<?php

namespace Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Dashboard\Models\Setting;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(base_path('dashboard/resources/views'), 'dashboard');

        try {
            \DB::connection()->getPdo();
            (new Setting)->loadSettings();
        } catch (\Exception $exception) {
            //
        }
    }
}
