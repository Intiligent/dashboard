<?php

namespace Dashboard\Policies;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Menu;
use App\Models\User;

class MenuPolicy
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
     * Delete menu item
     *
     * @param App\Models\User $user
     * @param App\Models\Menu $menu
     * @return bool|AuthorizationException
     */
    public function delete(User $user, Menu $menu)
    {
        if ($menu->is_system) {
            throw new AuthorizationException('Access denied delete system menu item');
        }
        return true;
    }
}
