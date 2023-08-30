<?php

namespace Dashboard\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('dashboard::components.header', 'Dashboard\Http\View\Composers\HeaderComposer');
        View::composer('dashboard::components.navbar', 'Dashboard\Http\View\Composers\NavbarComposer');

        // // Using Closure based composers...
        // View::composer('dashboard', function ($view) {
        //     //
        // });
    }
}
