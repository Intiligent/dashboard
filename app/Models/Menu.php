<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Menu extends Model
{
    use NodeTrait;

    const TYPE_ARTICLE = 'article';
    const TYPE_ROUTE = 'route';
    const TYPE_URI = 'uri';
    const TYPES = [
        self::TYPE_ARTICLE,
        self::TYPE_ROUTE,
        self::TYPE_URI,
    ];
    const TYPE_LIST = [
        [
            'name' => self::TYPE_URI,
            'title' => 'Url'
        ],
        [
            'name' => self::TYPE_ROUTE,
            'title' => 'Route',
        ],
        [
            'name' => self::TYPE_ARTICLE,
            'title' => 'Article',
        ],
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'type',
        'name',
        'value',
        'code',
        'attribute',
        'icon',
        'active',
        'order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'is_system',
        'code',
        'active',
        '_lft',
        '_rgt',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * Bootstrap model event.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            if (!$model->code) {
                $model->code = Str::slug($model->name);
            }
        });
    }

    /**
     * Get the computer url
     *
     * @return bool
     */
    public function getUrlAttribute()
    {
        if (!array_key_exists('value', $this->attributes)) {
            return;
        }
        if ($this->attributes['type'] === self::TYPE_ROUTE) {
            if (Route::has($this->attributes['value'])) {
                return route($this->attributes['value']);
            }
            return "unknown route name: {$this->attributes['value']}";
        }
        return $this->attributes['value'];
    }

    /**
     * Get navigation menu by code
     *
     * @param string $code [menu group code name]
     * @return Kalnoy\Nestedset\Collection
     */
    public static function getMenuByCode(string $code = 'main')
    {
        $menuRoot = Menu::where('code', $code)->first();
        return Menu::where('active', true)
            ->defaultOrder()
            ->descendantsOf($menuRoot->id)
            ->toTree();
    }

    /**
     * Get navigation menu from cache
     *
     * @param string $code [menu group code name]
     * @return Kalnoy\Nestedset\Collection
     */
    public static function getStoreMenu(string $code = 'main')
    {
        return Cache::rememberForever('navigation-menu-' . $code, function() use ($code) {
            return self::getMenuByCode($code);
        });
    }

    /**
     * Get the ancestor of menu item.
     */
    public function ancestor()
    {
        return self::ancestorsOf($this->id)->first();
    }
}
