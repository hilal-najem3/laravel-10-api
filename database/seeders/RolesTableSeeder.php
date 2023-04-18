<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Containers\Roles\Models\Role;
use Illuminate\Support\Facades\DB;
use Exception;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Has full access to everything'
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Has access to permissions that the super admin assigns to him'
            ],
            [
                'name' => 'Agency Admin',
                'slug' => 'agency-admin',
                'description' => 'Has access to permissions that the admins assigns to him concerning the editing of an agency\'s data'
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Has access to permissions that the super admin or admin assigns to him'
            ]
        ];
        DB::beginTransaction();
        try {
            foreach($roles as $role) {
                Role::create($role);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
