<?php

namespace App\Helpers\Database;

use App\Containers\Users\Permissions\Permissions as UserPermissions;
use App\Containers\Agencies\Permissions\Permissions as AgenciesPermissions;
use App\Containers\Common\Permissions\Permissions as CommonPermissions;
use App\Containers\Currencies\Permissions\Permissions as CurrenciesPermissions;
use App\Containers\Plans\Permissions\Permissions as PlansPermissions;

use Illuminate\Support\Facades\DB;
use App\Containers\Permissions\Models\Permission;
use App\Containers\Roles\Models\Role;

use Exception;

class PermissionsHelper
{
    /**
     * This function gets permission data that are
     * name and slug
     * It checks if permission exists
     * creates it if it doesn't
     * 
     * @param $permissionData
     * @return Permission $permission
     */
    public static function addPermission($permissionData): Permission
    {
        DB::beginTransaction();
        try {
            if(isset($permissionData['roles'])) {
                unset($permissionData['roles']);
            }
            $permission = Permission::create($permissionData);
            DB::commit();

            return $permission;
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    /**
     * This function seeds into the database all the permissions
     * needed to run in the api
     * 
     * @return void
     */
    public static function addPermissions(): void
    {
        $permissions = self::getPermissions();

        foreach($permissions as $perm) {
            self::addPermission($perm);
        }
    }

    /**
     * This function returns all this permissions in this api
     * 
     * @return array
     */
    public static function getPermissions()
    {
        $permissions = array();
        
        $userPermissions = UserPermissions::permissions();
        $agenciesPermissions = AgenciesPermissions::permissions();
        $commonPermissions = CommonPermissions::permissions();
        $currenciesPermissions = CurrenciesPermissions::permissions();
        $plansPermissions = PlansPermissions::permissions();

        $permissions = array_merge($permissions, $userPermissions);
        $permissions = array_merge($permissions, $agenciesPermissions);
        $permissions = array_merge($permissions, $commonPermissions);
        $permissions = array_merge($permissions, $currenciesPermissions);
        $permissions = array_merge($permissions, $plansPermissions);

        return $permissions;
    }

    /**
     * This function checks if if the permission is added to the database
     * and adds it if not
     * 
     * @param string $slug
     * @return void
     */
    public static function addBySlug(string $slug): void
    {
        try {
            if($slug && $slug != '') {
                $slug = trim($slug);

                $permissions = self::getPermissions();
                $permissionData = [];
                foreach($permissions as $perm) {
                    if($perm['slug'] == $slug) {
                        $permissionData = $perm;
                        break;
                    }
                }
                if(Permission::where('slug', $permissionData['slug'])->count() == 0) {
                    $perm = self::addPermission($permissionData);
                    if(isset($permissionData['roles']) && count($permissionData['roles'])) {
                        $admin_role = Role::where('slug', 'admin')->first();
                        $agency_admin = Role::where('slug', 'agency-admin')->first();
                        $admins = $admin_role->users()->get();
                        $agency_admins = $agency_admin->users()->get();

                        foreach($permissionData['roles'] as $roleId) {
                            $permission = Permission::where('slug', $permissionData['slug'])->first();
                            $permission->roles()->attach($roleId);
        
                            if($roleId == $admin_role->id) {
                                foreach($admins as $admin) {
                                    $admin->permissions()->attach($permission);
                                }
                            }
        
                            if($roleId == $agency_admin->id) {
                                foreach($agency_admins as $admin) {
                                    $admin->permissions()->attach($permission);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}