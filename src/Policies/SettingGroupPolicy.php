<?php

namespace Dashboard\Policies;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Models\User;
use Dashboard\Models\SettingGroup;

class SettingGroupPolicy
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
     * Удаление группы настроек
     *
     * @param App\Models\User $user
     * @param Dashboard\Models\SettingGroup $group
     * @return bool|AuthorizationException
     */
    public function delete(User $user, SettingGroup $group)
    {
        $count = $group->loadCount(['settings as count' => function($query) {
            $query->where('is_system', true);
        }])->count;
        if ($count) {
            throw new AccessDeniedHttpException('Group contain system setting and disallow drop');
        }
        return true;
    }
}
