<?php

namespace Viart\Dashboard;

use Viart\Dashboard\Console\DashboardInstall;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DashboardInstall::class,
            ]);
        }
    }
}
