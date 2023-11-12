<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'email_verified_at',
        'password',
        'remember_token',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The attributes that are sortable.
     *
     * @var array<string, string>
     */
    protected $sortable = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are filterable.
     *
     * @var array<string, string>
     */
    protected $filterable = [
        'id',
        'name',
        'email',
    ];

    /**
     * Set the password. make hash
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Apply sortable
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApplySort($query, Request $request)
    {
        $request->whenFilled('sortModel', function($sortModel) use ($query) {
            foreach ($sortModel as ['colId' => $columnName, 'sort' => $direction]) {
                if ($direction && in_array($columnName, $this->sortable)) {
                    $query->orderBy($columnName, $direction);
                }
            }
        });
        // $direction = $request->get('direction', '');
        // if (!in_array($direction, ['asc', 'desc', ''])) {
        //     $direction = 'asc';
        // }
        // $request->whenFilled('sort', function($columnName) use ($query, $direction) {
        //     if ($direction && in_array($columnName, $this->sortable)) {
        //         $query->orderBy($columnName, $direction);
        //     }
        // });
        if ($request->missing('sortModel')) {
            $query->latest('id');
        }
        return $query;
    }

    /**
     * Apply filter
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApplyFilter($query, Request $request)
    {
        $request->whenFilled('q', function($q) use ($query) {
            $query->where('id', 'LIKE', "%$q%")
                ->orWhere('name', 'LIKE', "%$q%")
                ->orWhere('email', 'LIKE', "%$q%");
        });

        $request->whenFilled('filterModel', function($filterModel) use ($query) {
            if (array_key_exists('name', $filterModel)) {
                $query->where('name', 'LIKE', "%".$filterModel['name']."%");
            }
            if (array_key_exists('email', $filterModel) && $filterModel['email']) {
                $query->where('email', 'LIKE', "%".$filterModel['email']."%");
            }
            if (array_key_exists('created_at', $filterModel)) {
                $query->whereBetween('created_at', $filterModel['created_at']);
            }
        });

        return $query;
    }
}
