<?php

namespace viart\dashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InstallPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:install
        {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the dashboard scaffolding';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $folderName = 'dashboard';
        
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                'axios' => '^0.18.0',
                'ckeditor4-vue' => '^1.3.0',
                'clipboard' => '^2.0.8',
                'element-ui' => 'npm:element-ui-viart@^2.15.3-8',
                'lodash' => '^4.17.21',
                'vue' => '^2.6.10',
                'vue-router' => '^3.5.1'
            ] + $packages;
        }, 'dependencies') && $this->line('✔ Update node modules dependencies');

        $this->updateNodePackages(function ($elements) {
            return [
                'presets' => [
                    '@babel/preset-env',
                ],
                'plugins' => [
                    [
                        'component',
                        [
                            'libraryName' => 'element-ui',
                            'style' => false,
                        ]
                    ]
                ],
            ] + $elements;
        }, 'babel') && $this->line('✔ Update package.json plugins');

        (new Filesystem)->copyDirectory(__DIR__.'/../Events', base_path("$folderName/Events"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Http', base_path("$folderName/Http"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Listeners', base_path("$folderName/Listeners"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Models', base_path("$folderName/Models"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Policies', base_path("$folderName/Policies"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Providers', base_path("$folderName/Providers"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Rules', base_path("$folderName/Rules"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../resources', base_path("$folderName/resources"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../routes', base_path("$folderName/routes"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../tests', base_path("$folderName/test"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../database/migrations', base_path("$folderName/database/migrations"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../database/seeders', base_path("$folderName/database/seeders"));
        $this->line('✔ Copy resources');

        copy(__DIR__.'/../../config/dashboard.php', base_path('config/dashboard.php'));
        copy(__DIR__.'/../../config/ziggy.php', base_path('config/ziggy.php'));
        copy(__DIR__.'/../../config/permission.php', base_path('config/permission.php'));
        copy(__DIR__.'/../../config/shared-data.php', base_path('config/shared-data.php'));
        $this->line('✔ Copy config files');

        copy(__DIR__.'/../../bootstrap/constants.php', base_path('bootstrap/constants.php'));
        $this->addRequireConstant() && $this->line('✔ Require constant file');
        copy(__DIR__.'/../../webpack.mix.js', base_path('webpack.mix.js'));

        $this->replaceInFile("// protected \$namespace = 'App\\Http\\Controllers';", "protected \$namespace = 'App\\Http\\Controllers';", app_path('Providers/RouteServiceProvider.php')) && $this->line('✔ Update route namespace');

        $this->updateComposer(function ($elements) use ($folderName) {
            $psr4 = [
                'psr-4' => [
                    'Dashboard\\' => "$folderName/",
                ],
            ];
            return array_merge_recursive_distinct($elements, $psr4);
        }, 'autoload') && $this->line('✔ Add dashboard autoload');

        $this->registerDashboardServiceProvider() && $this->line('✔ Register dashboard service providers');

        $this->registerDashboardGuard() && $this->line('✔ Register dashboard guard');

        $this->addPhpunitTestsuite() && $this->line('✔ Add phpunit testsuite');

        $this->requireComposerPackages([
            'coderello/laravel-shared-data:^3.0',
            'eusonlito/laravel-meta:3.1.*',
            'kalnoy/nestedset:^5.0',
            'spatie/laravel-permission:^4.2',
            'tightenco/ziggy:^0.9.4',
        ]);
        $this->line('✔ Install composer packages');

        $this->info('Dashboard scaffolding installed successfully.');
        $this->comment('Please execute the "npm install && npm run watch" command to build your assets.');
    }

    // private function publishResources($forcePublish = false)
    // {
    //     $params = [
    //         '--provider' => 'viart\dashboard\DashboardServiceProvider',
    //     ];
    //
    //     if ($forcePublish === true) {
    //         $params['--force'] = '';
    //     }
    //
    //    $this->call('vendor:publish', $params);
    // }

    /**
     * Update the "package.json" file.
     *
     * @param callable $callback
     * @param string $configurationKey [devDependencies|dependencies|babel]
     * @return mixed
     */
    protected function updateNodePackages(callable $callback, $configurationKey = 'dependencies')
    {
        if (!file_exists(base_path('package.json'))) {
            $this->error(base_path('package.json') . ' not found!');
            return false;
        }
        $packages = json_decode(file_get_contents(base_path('package.json')), true);
        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );
        ksort($packages[$configurationKey]);
        return file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Update the "composer.json" file.
     *
     * @param callable $callback
     * @param string $configurationKey [autoload]
     * @return mixed
     */
    protected function updateComposer(callable $callback, string $configurationKey)
    {
        if (!file_exists(base_path('composer.json'))) {
            $this->error(base_path('composer.json') . ' not found!');
            return false;
        }
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);
        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );
        ksort($packages[$configurationKey]);
        return file_put_contents(
            base_path('composer.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Register the Dashboard service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerDashboardServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());
        $dashboardNamespace = 'Dashboard';
        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $dashboardNamespace.'\\Providers\\DashboardServiceProvider::class')) {
            return false;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];
        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];
        $providers = "{$namespace}\\Providers\RouteServiceProvider::class,".$eol."
        /*
         * Dashboard Service Providers...
         */
        {$dashboardNamespace}\Providers\DashboardServiceProvider::class,
        {$dashboardNamespace}\Providers\AuthServiceProvider::class,
        {$dashboardNamespace}\Providers\EventServiceProvider::class,
        {$dashboardNamespace}\Providers\RouteServiceProvider::class,
        {$dashboardNamespace}\Providers\ViewServiceProvider::class,".$eol;

        return file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\RouteServiceProvider::class,".$eol,
            $providers,
            $appConfig
        ));
    }

    protected function registerDashboardGuard()
    {
        $guards = config('auth.guards') + [
            'dashboard' => [
                'driver' => 'session',
                'provider' => 'users',
            ]
        ];
        $formatGuards = $this->varExport($guards, true, '    ');
        $config = file_get_contents(config_path('auth.php'));
        $search = Str::before(Str::after($config, "'guards' => "), ",\n\n");
        $replace = str_replace($search, $formatGuards, $config);
        return file_put_contents(config_path('auth.php'), $replace);
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param mixed $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    protected function addRequireConstant()
    {
        $app = file_get_contents(base_path('bootstrap/app.php'));
        $require = "require_once __DIR__ . '/constants.php';";
        if (Str::contains($app, $require)) {
            return false;
        }
        $content = preg_replace("/(<[?]php\s+)/", '$1'.$require.PHP_EOL.PHP_EOL, $app);
        return file_put_contents(base_path('bootstrap/app.php'), $content);
    }

    public function addPhpunitTestsuite()
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->load(base_path('phpunit.xml'));
        $testsuites = $document->getElementsByTagName('testsuites')->item(0);
        $nodeFeature = $document->createDocumentFragment();
        $nodeFeature->appendXML('<testsuite name="FeatureDashboard"><directory suffix="Test.php">./dashboard/tests/Feature</directory></testsuite>');
        $testsuites->appendChild($nodeFeature);
        $nodeUnit = $document->createDocumentFragment();
        $nodeUnit->appendXML('<testsuite name="UnitDashboard"><directory suffix="Test.php">./dashboard/tests/Unit</directory></testsuite>');
        $testsuites->appendChild($nodeUnit);
        $content = preg_replace_callback('/^( +)</m', function($a) {
            return str_repeat(' ', intval(strlen($a[1]) / 2) * 4).'<';
        }, $document->saveXML());
        return file_put_contents(base_path('phpunit.xml'), $content);
    }

    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return mixed
     */
    protected function replaceInFile($search, $replace, $path)
    {
        return file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }

    function varExport($expression, $return = false, $tab = null)
    {
        $export = var_export($expression, true);
        $export = preg_replace("/^([ ]*)(.*)/m", $tab.'$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) {
            return $export;
        }
        echo $export;
    }
}
