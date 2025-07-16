<?php

namespace Viart\Dashboard\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Dotenv\Dotenv;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use DOMDocument;
use Exception;
use RuntimeException;

class DashboardInstall extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'dashboard:install
        {name?}
        {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Install the dashboard scaffolding';

    /**
     * Base name for the root folder name, route, guard
     *
     * @var string
     */
    protected $name;

    protected $views = [
        '/stubs/app/Enums/MenuType.php' => '/../app/Enums/MenuType.php',
        '/stubs/app/Models/Menu.php' => '/../app/Models/Menu.php',
        '/stubs/Http/Controllers/Auth/LoginController.stub' => '/Http/Controllers/Auth/LoginController.php',
        '/stubs/Http/Controllers/Controller.stub' => '/Http/Controllers/Controller.php',
        '/stubs/Http/Controllers/HomeController.stub' => '/Http/Controllers/HomeController.php',
        '/stubs/Providers/DashboardServiceProvider.stub' => '/Providers/DashboardServiceProvider.php',
        '/stubs/resources/views/auth/login.stub' => '/resources/views/auth/login.blade.php',
        '/stubs/resources/views/layouts/base.stub' => '/resources/views/layouts/base.blade.php',
        '/stubs/resources/views/layouts/default.stub' => '/resources/views/layouts/default.blade.php',
        '/stubs/resources/views/home/index.stub' => '/resources/views/home/index.blade.php',
        '/stubs/routes/api.stub' => '/routes/api.php',
        '/stubs/routes/web.stub' => '/routes/web.php',
    ];

    /**
     * Node modules dependencies
     *
     * @var array
     */
    private function composerPatch(): array
    {
        return [
            'autoload' => [
                'psr-4' => [
                    ucfirst($this->name) . '\\' => $this->name . '/',
                    ucfirst($this->name) . '\\Database\\Seeders\\' => $this->name . '/database/seeders/',
                ],
            ],
            'config' => [
                'sort-packages' => true,
            ],
        ];
    }

    protected function replacements(array $overrides = []): array
    {
        return array_merge([
            'name' => $this->name,
            'namespace' => ucfirst($this->name),
        ], $overrides);
    }

    public function handle()
    {
        $this->name = $this->argument('name')
            ?? $this->ask('Enter the base name (folder, route, guard)', 'dashboard');

        $this->updateRootComposer();
        $this->registerServiceProviders();
        $this->registerGuards();
        $this->addHtaccessRule();
        $this->addPhpUnitTestSuite();
        $this->addDotEnvExampleProperties();
        // $this->addDatabaseSeeder();
        $this->copyResources();
        $this->publishStubs();
        $this->addRequireConstant();

        $this->newLine();
        $this->newLine();
        (new Process(['composer', 'dump-autoload', '--optimize']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
        (new Process(['php', 'artisan', 'optimize']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
        
        $connection = null;
        if ($this->confirm('Do you wish to call migrations?', true)) {
            try {
                $connection = DB::connection()->getPdo();
                (new Process(['php', 'artisan', 'migrate']))
                    ->setTimeout(null)
                    ->run(function ($type, $output) {
                        $this->output->write($output);
                    });
            } catch (Exception $exception) {
                $this->error("Could not connect to the database. Please check your configuration. Error: " . $exception->getMessage());
            }
        }
        if ($connection && $this->confirm('Do you wish to seed database?', true)) {
            (new Process(['php', 'artisan', 'db:seed', '--class=Dashboard\\Database\\Seeders\\DashboardSeeder']))
                ->setTimeout(null)
                ->run(function ($type, $output) {
                    $this->output->write($output);
                });
        }
        if ($connection && $this->confirm('Create new superadmin user for dashboard?', true)) {
            $name = $this->askWithValidation('name', 'Input user name', ['required']);
            $email = $this->askWithValidation('email', 'Input user email', ['required', 'email', 'unique:users,email']);
            $password = $this->askWithValidation('name', 'Input user password', ['required', 'min:6'], 'secret');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            // $user->assignRole(['superadmin', 'admin'], 'dashboard');
            $user && $this->line("<fg=green>✓</> Create new user: $email");
        }
        if ($this->confirm('Do you wish to install node modules?', true)) {            
            $this->commandRun(['npm', 'install'], base_path($this->name));
            $this->commandRun(['npm', 'run', 'build'], base_path($this->name));
        }
        $this->newLine()->info('Dashboard scaffolding installed successfully');
        $this->info('If you dont have a local server please call "php artisan serve" for serve project.');
        $this->info('Go ahead: ' . url($this->name));
    }

    protected function updateRootComposer(): bool
    {
        $filePath = base_path('composer.json');
        if (!File::exists($filePath)) {
            $this->line('<fg=red>✕</> Update composer.json ' . $filePath . ' not found!');
            return false;
        }
        try {
            $package = json_decode(File::get($filePath), true);
            $composerPatch = $this->composerPatch();
            $updated = array_merge_recursive_distinct($package, $composerPatch);
            File::put(
                $filePath,
                json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
            );
            $this->line('<fg=green>✓</> Update composer.json file');
            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Update composer.json. ' . $exception->getMessage());
            return false;
        }
    }

    protected function registerServiceProviders(): bool
    {
        $provider = ucfirst($this->name) . '\Providers\DashboardServiceProvider';
        if (!method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            $this->line('<fg=red>✕</> Fail register service providers. Add manually: ' . $provider);
            return false;
        }
        ServiceProvider::addProviderToBootstrapFile($provider);
        $this->line('<fg=green>✓</> Register service providers.');

        return true;

        // $filePath = base_path('bootstrap/providers.php');

        // if (!File::exists($filePath)) {
        //     $this->line('<fg=red>✕</> Register service providers. File not found: ' . $filePath);
        //     return false;
        // }

        // try {
        //     $contents = File::get($filePath);
        //     $insertions = [];

        //     foreach ($this->providers() as $provider) {
        //         if (strpos($contents, $provider) === false) {
        //             $insertions[] = "    $provider,";
        //             $this->line("<fg=blue>+</> Add new service provider: $provider");
        //         } else {
        //             $this->line("<fg=yellow>ℹ</> Provider already exists: $provider");
        //         }
        //     }

        //     if (empty($insertions)) {
        //         $this->line('<fg=green>✓</> Register service providers.');
        //         return true;
        //     }

        //     $contents = preg_replace_callback('/(return\s*\[\s*)(.*?)(\s*];)/s', function ($matches) use ($insertions) {
        //         $lines = explode('\n', rtrim($matches[2]));

        //         // check on semicolum at the last line
        //         $lastLineIndex = count($lines) - 1;
        //         if (!str_ends_with(trim($lines[$lastLineIndex]), ',')) {
        //             $lines[$lastLineIndex] .= ',';
        //         }

        //         $lines = array_merge($lines, $insertions);

        //         return $matches[1] . implode("\n", $lines) . $matches[3];
        //     }, $contents);

        //     File::put($filePath, $contents);    
        //     $this->line('<fg=green>✓</> Register service providers.');        
        //     return true;
        // } catch (Exception $exception) {
        //     $this->line('<fg=red>✕</> Register service providers. ' . $exception->getMessage());
        //     return false;
        // }
    }

    protected function addHtaccessRule(): bool
    {
        $filePath = public_path('.htaccess');

        if (!File::exists($filePath)) {
            $this->line("<fg=red>✕</> Add htaccess rule. File not found at $filePath");
            return false;
        }

        try {
            $contents = File::get($filePath);

            $newRuleBlock = <<<HTACCESS
    # Exception for route /$this->name
    RewriteCond %{REQUEST_URI} ^/$this->name/?$
    RewriteRule ^ index.php [L]


HTACCESS;

            if (strpos($contents, "RewriteCond %{REQUEST_URI} ^/$this->name") !== false) {
                $this->line("<fg=green>✓</> Rule for /$this->name in .htaccess already exists");
                return true;
            }

            // Find place before block "Redirect Trailing Slashes..."
            $pattern = '/(^[ \t]*# Redirect Trailing Slashes If Not A Folder\.\.\..*?$)/m';

            if (preg_match($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {
                $insertPos = $matches[0][1]; // comment position
                $contents = substr_replace($contents, $newRuleBlock, $insertPos, 0);
                File::put($filePath, $contents);
                $this->line('<fg=green>✓</> Add htaccess rule');

                return true;
            } else {
                $this->line('<fg=red>✕</> Add htaccess rule. Could not find insert point in .htaccess');

                return false;
            }
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Add htaccess rule. ' . $exception->getMessage());

            return false;
        }
    }

    protected function addRequireConstant()
    {
        try {
            $app = File::get(base_path('bootstrap/app.php'));
            $require = "require_once __DIR__ . '/constants.php';";
            if (Str::contains($app, $require)) {
                $this->line("<fg=yellow>ℹ</> Add constants. Already exists");
                return false;
            }
            $content = preg_replace("/(<[?]php\s+)/", '$1' . $require . PHP_EOL . PHP_EOL, $app);
            $this->line('<fg=green>✓</> Add constants');

            return File::put(base_path('bootstrap/app.php'), $content);
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Add constants. ' . $exception->getMessage());

            return false;
        }
    }

    protected function registerGuards(): bool
    {
        $filePath = config_path('auth.php');

        if (!File::exists($filePath)) {
            $this->line("<fg=red>✕</> Register guards. File $filePath not found");
            return false;
        }

        try {
            $contents = File::get($filePath);

            if (config("auth.guards.$this->name")) {
                $this->line("<fg=green>✓</> Guard '$this->name' already exists");
                return true;
            }

            $guards = config('auth.guards') + [
                $this->name => [
                    'driver' => 'session',
                    'provider' => 'users',
                    'session' => $this->name . '_session',
                ],
            ];
            $formatted = "'guards' => " . array_export_pretty($guards, '    ') . ',';
            $pattern = '/(\'guards\'\s*=>\s*\[(?:[^[\]]|\[[^\]]*+\])*+\],)/s';
            $result = preg_replace($pattern, $formatted, $contents, 1);
            File::put($filePath, $result);
            $this->line("<fg=green>✓</> Register guards to auth.php");

            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Register guards. ' . $exception->getMessage());

            return false;
        }
    }

    protected function copyResources(): bool
    {
        try {
            File::ensureDirectoryExists(base_path($this->name));
            File::copy(__DIR__ . '/../constants.php', base_path('bootstrap/constants.php')) && $this->line(" ⊢ Published: " . base_path('bootstrap/constants.php'));
            File::copy(__DIR__ . '/../package.json', base_path("$this->name/package.json")) && $this->line(" ⊢ Published: " . base_path("$this->name/package.json"));
            File::copy(__DIR__ . '/../.gitignore', base_path("$this->name/.gitignore")) && $this->line(" ⊢ Published: " . base_path("$this->name/.gitignore"));
            File::copy(__DIR__ . '/../vite.config.js', base_path("$this->name/vite.config.js")) && $this->line(" ⊢ Published: " . base_path("$this->name/vite.config.js"));
            File::ensureDirectoryExists(public_path($this->name));
            File::copy(__DIR__ . '/../favicon.ico', public_path($this->name.'/favicon.ico')) && $this->line(" ⊢ Published: " . public_path("$this->name/favicon.ico"));
            File::copyDirectory(__DIR__.'/../resources', base_path("$this->name/resources")) && $this->line(" ⊢ Published: " . base_path("$this->name/resources"));
            File::copyDirectory(__DIR__.'/../../database', base_path("$this->name/database")) && $this->line(" ⊢ Published: " . base_path("$this->name/database"));
            $this->line("<fg=green>✓</> Copy resources");

            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Copy resources. ' . $exception->getMessage());

            return false;
        }
    }

    protected function publishStubs(): bool
    {
        try {
            foreach ($this->views as $stub => $destination) {
                $stubPath = __DIR__ . '/../../' . ltrim($stub, '/');
                $outputPath = base_path($this->name . $destination);

                if (!File::exists($stubPath)) {
                    $this->line("<fg=red>✕</> Stub not found: $stubPath");
                    continue;
                }

                $content = File::get($stubPath);
                $rendered = preg_replace_callback('/{{\s*(\w+)\s*}}/', function ($matches) use ($stub) {
                    $key = $matches[1];

                    if (!array_key_exists($key, $this->replacements())) {
                        throw new RuntimeException("Missing replacement for '{$key}' in stub {$stub}");
                    }

                    return $this->replacements()[$key];
                }, $content);
                File::ensureDirectoryExists(dirname($outputPath));
                File::put($outputPath, $rendered);
                $this->line(" ⊢ Published: {$outputPath}");
            }
            $this->line("<fg=green>✓</> Published stubs");
            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Publish stubs. ' . $exception->getMessage());
            return false;
        }
    }

    protected function addPhpunitTestsuite()
    {
        $filePath = base_path('phpunit.xml');

        if (!File::exists($filePath)) {
            $this->line("<fg=red>✕</> Add phpunit testsuite. File $filePath not found");
            return false;
        }

        try {
            $document = new DOMDocument();
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
            $document->load($filePath);
            $suites = [
                'FeatureDashboard' => "./$this->name/tests/Feature",
                'UnitDashboard' => "./$this->name/tests/Unit",
            ];
            $testsuites = $document->getElementsByTagName('testsuites')->item(0);
            $currentNames = array_map(function ($node) {
                return $node->getAttribute('name');
            }, iterator_to_array($testsuites->getElementsByTagName('testsuite')));
            $newItem = false;
            
            foreach ($suites as $name => $directory) {
                if (!in_array($name, $currentNames)) {
                    $nodeFeature = $document->createDocumentFragment();
                    $nodeFeature->appendXML('<testsuite name="'.$name.'"><directory suffix="Test.php">'.$directory.'</directory></testsuite>');
                    $testsuites->appendChild($nodeFeature);
                    $newItem = true;
                }
            }

            if ($newItem) {
                $content = preg_replace_callback('/^( +)</m', function($a) {
                    return str_repeat(' ', intval(strlen($a[1]) / 2) * 4).'<';
                }, $document->saveXML());
                File::put($filePath, $content);
                $this->line("<fg=green>✓</> Add phpunit testsuite");
            } else {
                $this->line("<fg=green>✓</> Add phpunit testsuite. Already exist");
            }

            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Add phpunit testsuite. ' . $exception->getMessage());

            return false;
        }
    }

    protected function addDotEnvExampleProperties(): bool
    {
        $filePath = base_path('.env.example');

        if (!File::exists($filePath)) {
            $this->line("<fg=red>✕</> Add .env.example properties. File $filePath not found");
            return false;
        }

        try {
            $vars = File::get($filePath);
            $dotenv = Dotenv::parse($vars);
            $properties = [
                'VITE_SERVER_HOST' => '',
                'VITE_SERVER_HMR_HOST' => '${VITE_SERVER_HOST}',
                'VITE_SERVER_PORT' => '',
                'VITE_SERVER_HTTPS_KEY' => '',
                'VITE_SERVER_HTTPS_CERT' => '',
            ];
            $missing = array_diff_key($properties, $dotenv);
            if (empty($missing)) {
                $this->line("<fg=green>✓</> Add .env.example properties. Already exist");
                return true;
            }

            $envString = implode(PHP_EOL, array_map(function ($key, $value) {
                return "$key=$value";
            }, array_keys($missing), $missing));
            File::append($filePath, PHP_EOL . $envString . PHP_EOL);
            $this->line("<fg=green>✓</> Add .env.example properties");

            return true;
        } catch (Exception $exception) {
            $this->line('<fg=red>✕</> Add phpunit testsuite. ' . $exception->getMessage());
            return false;
        }
    }

    protected function addDatabaseSeeder(): bool
    {
        $filePath = database_path('seeders/DatabaseSeeder.php');

        if (!File::exists($filePath)) {
            $this->line("<fg=red>✕</> Add database seeder. File not found: $filePath");
            return false;
        }

        try {
            $contents = File::get($filePath);
            $newSeeder = 'DashboardSeeder::class';
            $import = 'use Dashboard\Database\Seeders\DashboardSeeder;\n';

            if (!Str::contains($contents, trim($import))) {
                if (preg_match_all('/^use\s+[^\n]+;/m', $contents, $matches, PREG_OFFSET_CAPTURE)) {
                    $lastUse = end($matches[0]);
                    $pos = $lastUse[1] + strlen($lastUse[0]);
                    $contents = substr_replace($contents, "\n" . $import, $pos, 0);
                } elseif (preg_match('/^namespace\s+[^\n]+;/m', $contents, $nsMatch, PREG_OFFSET_CAPTURE)) {
                    $pos = $nsMatch[0][1] + strlen($nsMatch[0][0]);
                    $contents = substr_replace($contents, "\n\n" . $import, $pos, 0);
                }
            }

            if (Str::contains($contents, $newSeeder)) {
                $this->line("<fg=yellow>ℹ</> Seeder already exists in DatabaseSeeder.php");
                return true;
            }

            $pattern = '/\$this->call\s*\(\s*\[([^\]]*)\]/s';

            if (preg_match($pattern, $contents, $matches)) {
                $updatedBlock = rtrim($matches[1]) . "          $newSeeder,";
                $contents = preg_replace($pattern, '$this->call([' . $updatedBlock, $contents, 1);
            } else {
                $patternRun = '/public function run\(\): void\s*\{\s*/s';
                $callBlock = "\$this->call([\n            $newSeeder,\n        ]);\n\n";
                $contents = preg_replace($patternRun, '$0' . $callBlock, $contents, 1);
            }

            File::put($filePath, $contents);
            $this->line("<fg=green>✓</> Add database seeder");

            return true;
        } catch (\Exception $e) {
            $this->line('<fg=red>✕</> Add database seeder. ' . $e->getMessage());

            return false;
        }
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

    protected function commandRun(array $command, ?string $directory = null)
    {
        $process = new Process($command, $directory);

        try {
            $process->setTimeout(null)->mustRun(function ($type, $buffer) {
                $this->output->write($buffer);
            });
        } catch (ProcessFailedException $e) {
            $this->error($e->getMessage());
        }
    }
}
