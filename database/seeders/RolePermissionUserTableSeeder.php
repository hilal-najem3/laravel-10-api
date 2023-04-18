<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\Database\PermissionsHelper;
use Illuminate\Support\Facades\DB;
use App\Containers\Roles\Models\Role;
use App\Containers\Permissions\Models\Permission;
use Exception;

/**
 * This seeder adds permissions to roles
 * then assigns those added permissions to users of that role
 */
class RolePermissionUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $admin_role = Role::where('slug', 'admin')->first();
            $agency_admin = Role::where('slug', 'agency-admin')->first();

            $admins = $admin_role->users()->get();
            $agency_admins = $agency_admin->users()->get();

            $permissions = PermissionsHelper::getPermissions();
            foreach($permissions as $permissionData) {
                if(isset($permissionData['roles'])) {
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

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
