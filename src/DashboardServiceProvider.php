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
                __DIR__.'/../resources' => base_path('dashboard2/resources'),
                __DIR__.'/../routes' => base_path('dashboard2/routes'),
                __DIR__.'/Events' => base_path('dashboard2/Events'),
                __DIR__.'/Http' => base_path('dashboard2/Http'),
                __DIR__.'/Listeners' => base_path('dashboard2/Listeners'),
                __DIR__.'/Models' => base_path('dashboard2/Models'),
                __DIR__.'/Policies' => base_path('dashboard2/Policies'),
                __DIR__.'/Providers' => base_path('dashboard2/Providers'),
                __DIR__.'/Rules' => base_path('dashboard2/Rules'),

                __DIR__.'/../tests' => base_path('dashboard2/tests'),

                __DIR__.'/../database/migrations' => database_path('migrations2'),
                __DIR__.'/../database/seeders' => database_path('seeders2'),

                __DIR__.'/../webpack.mix.js' => base_path('webpack.mix.js'),
            ]);
        }
    }

    public function register()
    {
        //
    }
}
