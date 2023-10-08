<?php

namespace Dashboard\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Dashboard\Http\View\Composers\ErrorComposer;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('dashboard::errors.*', ErrorComposer::class);
        // View::composer('dashboard::components.navbar', 'Dashboard\Http\View\Composers\NavbarComposer');
        // View::composer('dashboard::components.navbar', 'Dashboard\Http\View\Composers\NavbarComposer');

        // // Using Closure based composers...
        // View::composer('dashboard', function ($view) {
        //     //
        // });
    }
}
