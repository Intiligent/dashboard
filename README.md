# Dashboard

## Install

1. Installation via Composer

```bash
composer require viart/dashboard
```

2. Install dashboard scaffolding

```bash
php artisan dashboard:install
```

3. make option create first user [email, password]

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

9. replace to dashboard

```php
/**
 * Get the administrator flag for the user.
 *
 * @param string path
 * @return string
 */
public function getAvatarAttribute($value)
{
    if (!$value) {
        return asset('/dashboard/img/fallback-user.png');
    }
    return $value;
}
```

10. в element-ui обновить деление на math.div()

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
