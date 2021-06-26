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

4. Register Service Provider in config/app.php in section `providers`:

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
