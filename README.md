# Dashboard

# Install

1. Installation Via Composer

```bash
composer require viart/dashboard
```

2. Publish files

```bash
php artisan dashboard:install
```

3. Add autoload path

```json
"autoload": {
    "psr-4": {
        "Dashboard\\": "dashboard/"
    }
},
```

4. Register Service Provider in `config/app.php` in section `providers`:

```php
'providers' => [
    /*
     * Dashboard Service Providers...
     */
    Dashboard\Providers\AuthServiceProvider::class,
    Dashboard\Providers\DashboardServiceProvider::class,
    Dashboard\Providers\EventServiceProvider::class,
    Dashboard\Providers\RouteServiceProvider::class,
    Dashboard\Providers\ViewServiceProvider::class,
],
```

5. Add guards to `config/auth.php`

```php
'guards' => [
    // ...
    'dashboard' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

6. Add `dashboard` log driver to `config/logging.php`

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

7. Require constants at the top of file `bootstrap/app.php`

```php
require_once __DIR__ . '/constants.php';
```

# Trouble Shooting Guide

```bash
php artisan vendor:publish --provider="viart\dashboard\DashboardServiceProvider" --tag="views"
```
output:
Unable to locate publishable resources.
Publishing complete.

**Solution:**

```bash
php artisan vendor:publish
```

ans choose number of published
