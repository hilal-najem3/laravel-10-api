<?php

namespace App\Containers\Users\Helpers;

use App\Containers\Roles\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserRolesHelper
{
    /**
     * This function returns the role that has the smallest id
     * which is the role with the highest priority for the current authenticated user.
     * 
     * @return Role $highestRole | null
     */
    public static function getCurrentHighestRole()
    {
        $currentUser = Auth::user();
        $currentUserRoles = $currentUser->roles()->get();
        $currentUserHighestRole = self::getHighestRole($currentUserRoles);
        return $currentUserHighestRole;
    }

    /**
     * This function receives an array of Role models
     * and returns the role that has the smallest id
     * which is the role with the highest priority.
     * 
     * @param Role[] $roles
     * @return Role $highestRole | null
     */
    public static function getHighestRole($roles)
    {
        if(count($roles) == 0) {
            // roles should be an array of Role model
            return null;
        }

        // In Case of roles the smallest role id is the highest role
        $highestRole = $roles[0];

        foreach($roles as $role) {
            if(!$role || !$highestRole || !$role->id || !$highestRole->id) {
                return null;
            }
            $role->id < $highestRole->id ? $highestRole = $role : $highestRole = $highestRole;
        }

        return $highestRole;
    }
}