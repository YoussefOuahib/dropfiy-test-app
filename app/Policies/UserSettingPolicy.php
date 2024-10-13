<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view their settings.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        // All authenticated users can view their own settings
        return true;
    }

    /**
     * Determine whether the user can update their settings.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        // All authenticated users can update their own settings
        return true;
    }
}