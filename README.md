# Dashboard

# Install

1. Installation Via Composer

```bash
composer require viart/dashboard
```

2. Install dashboard scaffolding

```bash
php artisan dashboard:install
```

3. Add `dashboard` log driver to `config/logging.php`

```php
'chanels' => [
    //...
    'dashboard' => [
        'driver' => 'single',
        'path' => storage_path('logs/dashboard.log'),
        'level' => 'debug',
    ],
],
```

4. Update Authenticate middleware in app/Http/Middleware/Authenticate.php

```php
/**
 * Get the path the user should be redirected to when they are not authenticated.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return string|null
 */
protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        if ($request->is('dashboard', 'dashboard/*')) {
            return route('dashboard.login');
        }
        return route('face.login');
    }
}
```

5. RedirectIfAuthenticated

```php
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @param  string|null  ...$guards
 * @return mixed
 */
public function handle($request, Closure $next, ...$guards)
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if ($guard === 'web' && Auth::guard($guard)->check()) {
            return redirect()->route('face.home');
        }
        if ($guard === 'dashboard' && Auth::guard($guard)->check()) {
            return redirect()->route('dashboard.home');
        }
    }

    return $next($request);
}
```

6. Check middleware

7. Exception

8. Controller response

```php
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
```

9. Copy app Models

- Role
- Permission
- User
- Media
- Menu

10. AppServiceProvider

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

11. RouteServiceProvider uncomment

```php
protected $namespace = 'App\\Http\\Controllers';
```

12. Copy file `deploy.sh`

13. Add test suitecase to `phpunit.xml`

```xml
<testsuites>
    <testsuite name="FeatureDashboard">
        <directory suffix="Test.php">./dashboard/tests/Feature</directory>
    </testsuite>
    <testsuite name="UnitDashboard">
        <directory suffix="Test.php">./dashboard/tests/Unit</directory>
    </testsuite>
</testsuites>
```

14. Set app routes
