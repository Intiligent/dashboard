<?php

namespace viart\dashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Models\User;

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
     * Folder name at the root of project.
     *
     * @var string
     */
    protected $folderName = 'dashboard';

    /**
     * Node modules dependencies.
     *
     * @var array
     */
    protected $dependencies = [
        'axios' => '^0.21.0',
        'ckeditor4-vue' => '^1.3.0',
        'clipboard' => '^2.0.8',
        'element-ui' => 'npm:element-ui-viart@*',
        'lodash' => '^4.17.21',
        'vue' => '^2.6.10',
        'vue-router' => '^3.5.1',
    ];

    /**
     * Node modules dev dependencies.
     *
     * @var array
     */
    protected $devDependencies = [
        'babel-plugin-component' => '^1.1.1',
        'resolve-url-loader' => '^4.0.0',
        'sass' => '^1.35.2',
        'sass-loader' => '^12.1.0',
        'vue-loader' => '^15.9.7',
        'vue-template-compiler' => '^2.6.14',
        'webpack-notifier' => '^1.13.0',
        'webpack-shell-plugin-next' => '^2.2.2',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $exception) {
            $this->error("Could not connect to the database. Please check your configuration. error: " . $exception->getMessage());
            return;
        }

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return $this->dependencies + $packages;
        }, 'dependencies') && $this->line('✔ Update node modules dependencies');

        $this->excludeDependencies($this->dependencies);

        $this->updateNodePackages(function ($packages) {
            return $this->devDependencies + $packages;
        }, 'devDependencies') && $this->line('✔ Update node modules dev dependencies');

        $this->excludeDependencies($this->devDependencies, false);

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

        $this->copyResources() && $this->line('✔ Copy resources');

        $this->copyConfigFiles() && $this->line('✔ Copy config files');

        $this->addRequireConstant() && $this->line('✔ Require constant file');

        $this->registerServiceProvider() && $this->line('✔ Register service providers');

        $this->registerDashboardGuard() && $this->line('✔ Register dashboard guard');

        $this->addDatabaseSeeder() && $this->line('✔ Add database seeder');

        $this->addPhpunitTestsuite() && $this->line('✔ Add phpunit testsuite');

        $this->updateAuthenticateMiddleware() && $this->line('✔ Update authenticate middleware');

        $this->updateComposer(function ($elements) {
            $psr4 = [
                'psr-4' => [
                    'Dashboard\\' => "$this->folderName/",
                ],
            ];
            return array_merge_recursive_distinct($elements, $psr4);
        }, 'autoload') && $this->line('✔ Add dashboard autoload');

        // $this->requireComposerPackages([
        //     'coderello/laravel-shared-data:^3.0',
        //     'eusonlito/laravel-meta:3.1.*',
        //     'kalnoy/nestedset:^6.0',
        //     'laravel/ui:^3.3',
        //     'spatie/laravel-permission:^4.2',
        //     'tightenco/ziggy:^0.9.4',
        // ]) === 0 && $this->line('✔ Install composer packages');

        $this->updateComposerAutoload();

        // $this->call('cache:clear');
        $this->call('config:cache');

        $this->comment('Please execute the "php artisan migrate && npm install && npm run watch" command to build your assets.');

        if ($this->confirm('Do you wish to call migrations?', true)) {
            $this->call('migrate');
        }
        if ($this->confirm('Do you wish to seed database?', true)) {
            $this->call('db:seed');
        }
        if ($this->confirm('Create new superadmin user for dashboard?', true)) {
            $name = $this->askWithValidation('name', 'Input user name', ['required']);
            $email = $this->askWithValidation('email', 'Input user email', ['required', 'email', 'unique:users,email']);
            $password = $this->askWithValidation('name', 'Input user password', ['required', 'min:6'], 'secret');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            $user->assignRole(['superadmin', 'admin'], 'dashboard');
            $user && $this->line("✔ Create new user: $email");
        }
        if ($this->confirm('Do you wish to install node modules?', true)) {
            (new Process(['npm', 'install'], base_path()))
                ->setTimeout(null)
                ->run(function ($type, $output) {
                    $this->output->write($output);
                });
        }
        $this->info('Dashboard scaffolding installed successfully');
        $this->info('Run "npm run watch" for build script files and then go ahead => ' . route('dashboard.home'));
    }

    /**
     * Copy resources file
     *
     * @return bool
     */
    protected function copyResources()
    {
        (new Filesystem)->copyDirectory(__DIR__.'/../Events', base_path("$this->folderName/Events"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Http', base_path("$this->folderName/Http"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Listeners', base_path("$this->folderName/Listeners"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Models', base_path("$this->folderName/Models"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Policies', base_path("$this->folderName/Policies"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Providers', base_path("$this->folderName/Providers"));
        (new Filesystem)->copyDirectory(__DIR__.'/../Rules', base_path("$this->folderName/Rules"));
        (new Filesystem)->copyDirectory(__DIR__.'/../resources', base_path("$this->folderName/resources"));
        (new Filesystem)->copyDirectory(__DIR__.'/../routes', base_path("$this->folderName/routes"));
        (new Filesystem)->copyDirectory(__DIR__.'/../tests', base_path("$this->folderName/test"));
        (new Filesystem)->copyDirectory(__DIR__.'/../../database/migrations', base_path('database/migrations'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../database/seeders', base_path('database/seeders'));
        (new Filesystem)->copyDirectory(__DIR__.'/../app/Models', base_path('app/Models'));

        copy(__DIR__.'/../../bootstrap/constants.php', base_path('bootstrap/constants.php'));
        copy(__DIR__.'/../../webpack.mix.js', base_path('webpack.mix.js'));

        return true;
    }

    /**
     * Copy config files
     *
     * @return bool
     */
    protected function copyConfigFiles()
    {
        copy(__DIR__.'/../../config/dashboard.php', base_path('config/dashboard.php'));
        copy(__DIR__.'/../../config/ziggy.php', base_path('config/ziggy.php'));
        copy(__DIR__.'/../../config/permission.php', base_path('config/permission.php'));
        copy(__DIR__.'/../../config/shared-data.php', base_path('config/shared-data.php'));
        copy(__DIR__.'/../../build.sh', base_path('build.sh'));
        return true;
    }

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
     * exclude dependencies from package json
     *
     * @param array $packages
     * @param boolean $dev
     * @return mixed
     */
    protected function excludeDependencies(array $packages, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            $this->error(base_path('package.json') . ' not found!');
            return false;
        }
        $value = json_decode(file_get_contents(base_path('package.json')), true);
        $dependency = $dev ? 'devDependencies' : 'dependencies';
        $value[$dependency] = array_diff_key($value[$dependency], $packages);
        ksort($value[$dependency]);
        return file_put_contents(
            base_path('package.json'),
            json_encode($value, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
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
    protected function registerServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());
        $dashboardNamespace = 'Dashboard';
        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $dashboardNamespace.'\\Providers\\DashboardServiceProvider::class')) {
            return false;
        }

        $providers = "
        /*
         * Package Service Providers...
         */
        Tightenco\Ziggy\ZiggyServiceProvider::class,
        Eusonlito\LaravelMeta\MetaServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,

        /*
         * Dashboard Service Providers...
         */
        {$dashboardNamespace}\Providers\DashboardServiceProvider::class,
        {$dashboardNamespace}\Providers\AuthServiceProvider::class,
        {$dashboardNamespace}\Providers\EventServiceProvider::class,
        {$dashboardNamespace}\Providers\RouteServiceProvider::class,
        {$dashboardNamespace}\Providers\ViewServiceProvider::class,";

        $content = preg_replace('/.*(App\\\\Providers\\\\\w*::class\,)/su', '$0'.PHP_EOL.$providers, $appConfig);

        return file_put_contents(config_path('app.php'), $content);
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
        $replace = "'guards' => $formatGuards";
        $result = preg_replace('/\'guards\' =>\s?([^\[\]]+\[[^\[\]]+\[[^\[\]]+\][^\[\]]+\[[^\[\]]+\][^\[\]]+\])/si', $replace, $config, 1);
        return file_put_contents(config_path('auth.php'), $result);
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (! Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after.',',
                $after.','.PHP_EOL.'            '.$name.',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }

    /**
     * Update authenticate middleware. add redirect to dashboard if auth
     *
     * @return void
     */
    protected function updateAuthenticateMiddleware()
    {
        $middleware = file_get_contents(app_path('Http/Middleware/Authenticate.php'));
        $code = "if (\$request->is('dashboard', 'dashboard/*')) {
                return route('dashboard.login');
            }";
        $content = preg_replace('/(redirectTo\([^)]*\)\s*\{\s*if\s*\([^)]*\)\)\s*{\s*)(return[^;]*;)(\s*}\s*})/si', '$1'.$code.PHP_EOL."\t\t\t$2$3", $middleware);
        return file_put_contents(app_path('Http/Middleware/Authenticate.php'), $content);
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param mixed $packages
     * @return int (0|1)
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

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Update composer autoload
     *
     * @return void
     */
    protected function updateComposerAutoload()
    {
        $command = ['composer', 'dump-autoload'];
        return (new Process($command, base_path(), ['--optimize-autoloader']))
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

    public function addDatabaseSeeder()
    {
        $fileContent = file_get_contents(database_path('seeders/DatabaseSeeder.php'));
        $seeders = [
            'SettingSeeder::class',
            'MenuSeeder::class',
            'UserSeeder::class',
            'AclSeeder::class',
            'ArticleSeeder::class',
        ];
        preg_match('/(\-\>call\(\[\s+)([^\]]+)(\n\s+?\]\))/si', $fileContent, $caller);
        if ($caller) {
            $existSeeders = explode(',', rtrim(trim(preg_replace('/\s+/', '', $caller[2])), ','));
            $needles = array_values(array_diff($seeders, $existSeeders));
            if (!$needles) {
                return false;
            }
            $result = preg_replace('/(\-\>call\(\[\s+)([^\]]+)(\n\s+?\]\))/si', "$1$2\n            ".$this->formatArray($needles)."$3", $fileContent);
        } else {
            $call = "\$this->call([
            ".$this->formatArray($seeders)."
        ]);";
            $result = preg_replace('/(public\sfunction\srun\(\).+?{\s+)([^}]+)(\n\s+?})/si', "$1$2".PHP_EOL.PHP_EOL."        $call$3", $fileContent);
        }
        return file_put_contents(database_path('seeders/DatabaseSeeder.php'), $result);
    }

    protected function formatArray(array $array)
    {
        $index = 0;
        return array_reduce($array, function($carry, $item) use (&$index, $array) {
            $indent = '';
            $separator = '';
            if ($index > 0) {
                $indent = '            ';
            }
            if (count($array) > $index + 1) {
                $separator = PHP_EOL;
            }
            $carry .= "$indent$item,$separator";
            $index++;
            return $carry;
        }, '');
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
     * Command line input value with validation
     *
     * @param string $name [field name]
     * @param string $message [ask message]
     * @param array  $rules [validation rules]
     * @param string $type [ask|secret]
     * @return string [input value]
     */
    protected function askWithValidation(string $name, string $message, array $rules = [], string $type = 'ask')
    {
        $answer = $this->$type($message);
        $validator = Validator::make([$name => $answer], [$name => $rules]);
        if ($validator->passes()) {
            return $answer;
        }
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }
        return $this->askWithValidation($name, $message, $rules, $type);
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
