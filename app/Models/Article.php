<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Language;

class Article extends Model
{
    use HasFactory;

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
        'lang_id',
        'slug',
        'title',
        'text',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
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
                // produce a slug based on the title
                $slug = Str::slug($model->title);
                // check to see if any other slugs exist that are the same & count them
                $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                // if other slugs exist that are the same, append the count to the slug
                $model->slug = $count ? "{$slug}-{$count}" : $slug;
            }
        });
    }

    /**
     * Get the language associated with the article.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    /**
     * Scope a query to apply filters.
     */
    public function scopeApplyFilters(Builder $query, $request): void
    {
        $request->whenFilled('q', function(string $input) use ($query) {
            $query->where('title', 'LIKE', "%$input%")
                ->orWhere('text', 'LIKE', "%$input%");
        });

        $request->whenFilled('filterModel', function(array $input) use ($query) {
            if (array_key_exists('active', $input) && is_array($input['active'])) {
                $query->whereIn('active', $input['active']);
            }
            if (array_key_exists('created_at', $input) && is_array($input['created_at'])) {
                $query->whereBetween('created_at', $input['created_at']);
            }
            if (array_key_exists('lang_id', $input) && is_array($input['lang_id'])) {
                $query->whereIn('lang_id', $input['lang_id']);
            }
            if (array_key_exists('title', $input) && $input['title']) {
                $query->where('title', 'LIKE', "%".$input['title']."%");
            }
        });
    }

    /**
     * Scope a query to apply sort.
     */
    public function scopeApplySort(Builder $query, $request): void
    {
        //
    }
}
