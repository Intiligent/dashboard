<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use App\Enums\MenuType;

class Menu extends Model
{
    use NodeTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'navigation';

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
     * Get the computed url
     *
     * @return bool
     */
    public function getUrlAttribute()
    {
        if (!array_key_exists('value', $this->attributes)) {
            return;
        }
        if ($this->attributes['type'] === MenuType::ROUTE) {
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

        if (!$menuRoot) {
            return;
        }

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
