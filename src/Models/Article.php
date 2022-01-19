<?php

namespace Dashboard\Models;

use App\Models\Article as ArticleBase;

class Article extends ArticleBase
{
    /**
     * Get the article image
     */
    public function image()
    {
        return $this->morphOne(Media::class, 'model')->where('entity', 'default');
        // ->withDefault([
        //     'path' => '/img/dashboard/fallback-thumb.jpg',
        // ]);
    }

    /**
     * Get the gallery image.
     */
    public function gallery()
    {
        return $this->morphMany(Media::class, 'model')->where('entity', 'gallery');
    }
}
