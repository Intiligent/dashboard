<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'slug',
        'text',
        'views',
        'active',
        'order',
    ];

    /**
     * The attributes that are sortable.
     *
     * @var array
     */
    protected $sortable = [
        'id',
        'title',
        'description',
        'slug',
        'text',
        'views',
        'active',
        'order',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime:d.m.Y H:i',
    ];

    /**
     * Bootstrap model event.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            if (!$model->slug) {
                // produce a slug based on the name
                $slug = Str::slug($model->name);
                // check to see if any other slugs exist that are the same & count them
                $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                // if other slugs exist that are the same, append the count to the slug
                $model->slug = $count ? "{$slug}-{$count}" : $slug;
            }
        });
    }

    /**
     * Set the description. Limit by length to 191 symbol
     *
     * @param string $value
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Str::limit($value, 191, '…');
    }

    /**
     * Get article by slug
     *
     * @param string $slug [url identifier]
     * @return Article
     */
    public static function getArticleBySlug(string $slug)
    {
        return Article::where('slug', $slug)
            ->where('active', true)
            ->first();
    }

    /**
     * Fetch article from cache or db
     *
     * @param string $slug [url identifier]
     * @return Article
     */
    public static function getStoreArticle(string $slug)
    {
        return Cache::rememberForever('article-page-' . $slug, function() use ($slug) {
            return self::getArticleBySlug($slug);
        });
    }

    /**
     * Apply sortable
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApplySort($query, Request $request)
    {
        $direction = $request->get('direction');
        if (!in_array($direction, ['asc', 'desc', ''])) {
            $direction = 'asc';
        }
        $request->whenFilled('sort', function($columnName) use ($query, $direction) {
            if ($direction && in_array($columnName, $this->sortable)) {
                $query->orderBy($columnName, $direction);
            }
        });
        if ($request->missing('sort')) {
            $query->latest();
        }
        return $query;
    }

    /**
     * Get the gallery image.
     */
    public function gallery()
    {
        return $this->morphMany(Media::class, 'model')->where('entity', 'gallery');
    }
}
