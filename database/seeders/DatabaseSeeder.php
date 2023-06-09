<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RegionTypesAndRegionsTablesSeeder::class,
            ImageTypesTableSeeder::class,
            DataTypesTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            UsersTableSeeder::class,
            ContactTypesTableSeeder::class,
            CurrenciesTableSeeder::class,
            RolePermissionUserTableSeeder::class,
            AgencyWithAgencyTypesTableSeeder::class
        ]);
    }
}
