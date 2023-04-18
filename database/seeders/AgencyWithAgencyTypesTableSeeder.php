<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Containers\Agencies\Models\AgencyType;
use App\Containers\Agencies\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

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
            $user = User::find(3);
            $user->agencies()->attach($agency);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
