<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Database\PermissionsHelper;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionsTableSeeder extends Seeder
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
            PermissionsHelper::addPermissions();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
