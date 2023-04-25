<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Containers\Agencies\Models\AgencyType;
use App\Containers\Agencies\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Containers\Roles\Models\Role;

class AgencyWithAgencyTypesTableSeeder extends Seeder
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
            $agency_admin = Role::where('slug', 'agency-admin')->first();

            $type = ['name' => 'Type 1', 'description' => 'Agency of type 1'];
            AgencyType::create($type);
            $agencyData = [
                'name' => 'Agency 1',
                'username' => 'Agency1',
                'type_id' => 1,
                'bio' => 'Some data about agency',
                'active' => true
            ];
            $agency = Agency::create($agencyData);
            $user = $agency_admin->users()->first();
            $user->agencies()->attach($agency);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
