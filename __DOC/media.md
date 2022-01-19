# Media file gallery

### Add single relation to model

```php
<?php

/**
 * Get the default image
 */
public function image()
{
    return $this->morphOne(Media::class, 'model')->where('entity', 'default');
}
```

Also, you can add fallback image.

```php
<?php
/**
 * Get the default image
 */
public function image()
{
    return $this->morphOne(Media::class, 'model')
        ->where('entity', 'default');
        ->withDefault([
            'path' => '/img/dashboard/fallback-thumb.jpg',
        ]);
}
```

### Add multiple relations

```php
<?php
/**
 * Get the gallery image.
 */
public function gallery()
{
    return $this->morphMany(Media::class, 'model')->where('entity', 'gallery');
}
```

### Using media relations (in controller)

```php
<?php

/**
 * Show article page
 *
 * @return Illuminate\Support\Facades\View
 */
public function showArticlePage($id)
{
    $article = Article::with(['image', 'gallery'])->findOrNew($id);
    return view('dashboard::pages.article.item', [
        'model' => $article,
    ]);
}

```

### Media model

```php
<?php

[
    "id" => 345,
    "model_type" => "App\Models\Article",
    "model_id" => 4,
    "entity" => "default",
    "user_id" => 1,
    "path" => "storage/article/origin-file-name.png",
    "title" => null,
    "description" => null,
    "active" => 1,
    "order" => 1,
    "created_at" => "2021-11-27 18:04:05",
    "updated_at" => "2021-11-27 18:04:05",
]

```
