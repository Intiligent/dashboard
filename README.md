<p align="center"><a href="https://packagist.org/packages/viart/dashboard"><img src="https://raw.githubusercontent.com/Intiligent/dashboard/main/src/resources/img/logo.png"></img></a></p>

# Dashboard

Dashboard panel for Laravel project.

## Install

1. Installation via Composer

```bash
composer require viart/dashboard
```

2. Install dashboard scaffolding

```bash
php artisan dashboard:install
```

3. Check middleware

7. Exception

8. dashboard 404

[Documentation](__DOC/index.md)

## Improve app

1. Add db string limited

```php
namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
```

2. RouteServiceProvider uncomment

```php

protected $namespace = 'App\\Http\\Controllers';
```

3. Add response and pass csrf token to front

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Coderello\SharedData\Facades\SharedData;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        SharedData::put([
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Returns REST response
     *
     * @param array|integer $error Error Code or array of params
     * @param array|null $params Array of additional params
     *
     * @return REST array
     */
    protected function response($params = array()) {
        $response = Arr::only($params, [ERR, CODE, MSG, DATA]);
        if (!isset($response[ERR])) {
            $response[ERR] = Response::HTTP_OK;
        }
        return $response;
    }
}
```

# Possible troubleshooting

#### Fatal error: Allowed memory size of 1610612736 bytes exhausted

```
COMPOSER_MEMORY_LIMIT=-1 composer require viart/dashboard
```

#### Class 'Dashboard\Providers\DashboardServiceProvider' not found

call command bellow and rerun `composer require viart/dashboard` again
```
rm -f bootstrap/cache/*.php
```

#### No application encryption key has been specified.

```
php artisan key:generate
```
