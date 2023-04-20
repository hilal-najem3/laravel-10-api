<?php

namespace App\Containers\Users\Helpers;

use App\Containers\Roles\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\Common\UpdateFailedException;
use Exception;

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

    /**
     * This function adds the permissions assigned to the user received
     * so that he has the same permissions as his role
     * 
     * @param User $user
     * @return void | UpdateFailedException
     */
    public static function addRolesPermissionsToUser(User $user): void
    {
        DB::beginTransaction();
        try {
            $roles = $user->roles()->get();
            foreach ($roles as $role) {
                $permissions = $role->permissions()->get();
    
                foreach($permissions as $permission) {
                    $user->permissions()->attach($permission);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException('USERS.UPDATE_USER_FAILED');
        }
    }

    /**
     * This function adds the permissions assigned to the user received
     * so that he has the same permissions as his role
     * 
     * @param User $user
     * @param int $roleId
     * @return void | UpdateFailedException
     */
    public static function addRolePermissionsToUser(User $user, int $roleId): void
    {
        DB::beginTransaction();
        try {
            $role = Role::find($roleId);
            $permissions = $role->permissions()->get();
            foreach($permissions as $permission) {
                $user->permissions()->attach($permission);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException('USERS.UPDATE_USER_FAILED');
        }
    }

    /**
     * This function adds the permissions assigned to the user received
     * so that he has the same permissions as his role
     * 
     * @param User $user
     * @param int $roleId
     * @return void | UpdateFailedException
     */
    public static function removeRolePermissionsToUser(User $user, int $roleId): void
    {
        DB::beginTransaction();
        try {
            $role = Role::find($roleId);
            $permissions = $role->permissions()->get();
            foreach($permissions as $permission) {
                $user->permissions()->detach($permission);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException('USERS.UPDATE_USER_FAILED');
        }
    }
}