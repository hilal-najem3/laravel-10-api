<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Containers\Common\Models\ContactType;
use Illuminate\Support\Facades\DB;
use Exception;

class ContactTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'email'],
            ['name' => 'phone'],
            ['name' => 'facebook'],
            ['name' => 'instagram']
        ];
        DB::beginTransaction();
        try {
            foreach($types as $type) {
                ContactType::create($type);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
