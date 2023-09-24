<?php

namespace Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Dashboard\Macros\BulkUpdateMacros;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/pulse.php', 'pulse');
        }
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
        $this->loadMigrationsFrom(base_path('dashboard/database/migrations'));
        
        // setup macro
        Builder::macro('bulkUpdate', function(array $values, $uniqueBy, array $updates) {
            return (new BulkUpdateMacros($this, $values, $uniqueBy, $updates))->handle();
        });
    }
}
