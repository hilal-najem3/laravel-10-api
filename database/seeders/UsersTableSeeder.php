<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Containers\Roles\Models\Role;
use App\Containers\Permissions\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;

class UsersTableSeeder extends Seeder
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
            $super_admin_role = Role::where('slug', 'super-admin')->first();
            $admin_role = Role::where('slug', 'admin')->first();
            $agency_admin = Role::where('slug', 'agency-admin')->first();
            $user_role = Role::where('slug', 'user')->first();

            $admin = User::create([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'super-admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
            $admin->roles()->attach($super_admin_role);

            $admin = User::create([
                'first_name' => 'Normal',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
            $admin->roles()->attach($admin_role);

            $admin = User::create([
                'first_name' => 'Agency',
                'last_name' => 'Admin',
                'email' => 'agency-admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
            $admin->roles()->attach($agency_admin);

            $user = User::create([
                'first_name' => 'User',
                'last_name' => 'User',
                'email' => 'user@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
            $user->roles()->attach($user_role);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
