<?php

namespace Dashboard\Policies;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use Dashboard\Models\Setting;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Редактирование настройки
     *
     * @param App\Models\User $user
     * @param Dashboard\Models\Setting $setting
     * @return bool|AuthorizationException
     */
    public function put(User $user, Setting $setting)
    {
        if ($setting->is_system) {
            throw new AuthorizationException('Access denied to edit system preference');
        }
        return true;
    }

    /**
     * Удаление настройки
     *
     * @param App\Models\User $user
     * @param Dashboard\Models\Setting $setting
     * @return bool|AuthorizationException
     */
    public function delete(User $user, Setting $setting)
    {
        if ($setting->is_system) {
            throw new AuthorizationException('Access denied delete system preference');
        }
        return true;
    }
}
