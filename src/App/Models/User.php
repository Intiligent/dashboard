<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d.m.Y H:i',
    ];

    /**
     * The attributes that are sortable.
     *
     * @var array
     */
    protected $sortable = [
        'id',
        'name',
        'email',
        'phone',
        'created_at',
        'updated_at',
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
        $direction = $request->get('direction', '');
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

    public function assignRole($roles, string $guard = null)
    {
        $roles = \is_string($roles) ? [$roles] : $roles;
        $guard = $guard ? : $this->getDefaultGuardName();
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) use ($guard) {
                return $this->getStoredRole($role, $guard);
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->all();
        $this->roles()->saveMany($roles);
        $this->forgetCachedPermissions();
        return $this;
    }

    protected function getStoredRole($role, string $guard): Role
    {
        if (\is_string($role)) {
            return app(Role::class)->findByName($role, $guard);
        }
        return $role;
    }
}
