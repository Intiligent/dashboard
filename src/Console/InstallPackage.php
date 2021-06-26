<?php

namespace viart\dashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPackage extends Command
{
    /**
     * Exclude the command from the list of Artisan commands
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the dashboard';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Installing Dashboard...');

        $this->info('Publishing configuration...');
        $this->publishConfiguration();
        $this->info('Published configuration');

        $this->info('Publishing resources...');
        $this->publishResources();
        $this->info('Published resources...');

        $this->info('Installed dashboard');
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => 'dashboard\DashboardServiceProvider',
            '--tag' => 'config',
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

       $this->call('vendor:publish', $params);
    }

    private function publishResources($forcePublish = false)
    {
        $params = [
            '--provider' => 'viart\dashboard\DashboardServiceProvider',
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

       $this->call('vendor:publish', $params);
    }
}
