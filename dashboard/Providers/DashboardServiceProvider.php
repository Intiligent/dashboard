<?php

namespace Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
// use Dashboard\Models\Setting;

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
        $this->loadTranslationsFrom(base_path('dashboard/resources/lang'), 'dashboard');

        // try {
        //     \DB::connection()->getPdo();
        //     (new Setting)->loadSettings();
        // } catch (\Exception $exception) {
        //     //
        // }
    }
}
