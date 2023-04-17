<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Containers\Currencies\Models\Currency;
use Illuminate\Support\Facades\DB;
use Exception;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Lebanese Lyra', 'symbol' => 'L.L.'],
            ['name' => 'American Dollar', 'symbol' => '$'],
        ];
        DB::beginTransaction();
        try {
            foreach($types as $type) {
                Currency::create($type);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
