<?php

namespace viart\dashboard;

use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallPackage::class,
            ]);

            // views, style, script, fonts, img
            $this->publishes([
                __DIR__.'/../config/dashboard.php' => config_path('dashboard.php'),
                __DIR__.'/../resources' => base_path('dashboard/resources'),
                __DIR__.'/../routes' => base_path('dashboard/routes'),
                __DIR__.'/Events' => base_path('dashboard/Events'),
                __DIR__.'/Http' => base_path('dashboard/Http'),
                __DIR__.'/Listeners' => base_path('dashboard/Listeners'),
                __DIR__.'/Models' => base_path('dashboard/Models'),
                __DIR__.'/Policies' => base_path('dashboard/Policies'),
                __DIR__.'/Providers' => base_path('dashboard/Providers'),
                __DIR__.'/Rules' => base_path('dashboard/Rules'),

                __DIR__.'/../tests' => base_path('dashboard/tests'),

                __DIR__.'/../database/migrations' => database_path('migrations'),
                __DIR__.'/../database/seeders' => database_path('seeders'),

                __DIR__.'/../webpack.mix.js' => base_path('webpack.mix.js'),
            ]);
        }
    }

    public function register()
    {
        //
    }
}
