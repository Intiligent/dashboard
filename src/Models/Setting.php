<?php

namespace Dashboard\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Dashboard\Models\Component;

class Setting extends Model
{
    use SoftDeletes;

    const CACHE_KEY = 'settings';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'key',
        'value',
        'name',
        'description',
        'type',
        'icon',
        'active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
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

        // self::saving(function($model) {
        //     $model->key = Str::of($model->key)->slug('_')->upper();
		// });
	}

    /**
     * Set the setting key to uppercase
     *
     * @param string $value [key]
     * @return void
     */
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = (string)Str::of($value)->slug('_')->upper();
    }

    /**
     * Setting group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(SettingGroup::class);
    }

    /**
     * Media file relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function media()
    {
        return $this->morphOne(Media::class, 'model');
    }

    /**
     * Fetch all setting from cache store
     *
     * @return array
     */
    public function getStore(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function() {
            return $this->getSettings()->toArray();
        });
    }

    /**
     * Fetch all setting
     *
     * @return Illuminate\Support\Collection
     */
    public function getSettings()
    {
        return self::select(['id', 'type', 'key', 'value'])
            // ->with(['media'])
            ->get()
            // ->transform(function ($setting, $key) {
            //      $setting->getAttributes()['type'] -> $setting->type
            //     if ($setting->getAttributes()['type'] === Component::MEDIA_FILE && $setting->media) {
            //         $setting->value = $setting->media->path;
            //     }
            //     return $setting;
            // })
            ->pluck('value', 'key');
    }

    /**
     * Load settings to app
     *
     * @return void
     */
    public function loadSettings()
    {
        $settings = $this->getStore();
        if ($settings) {
            config($settings);
        }
    }
}
