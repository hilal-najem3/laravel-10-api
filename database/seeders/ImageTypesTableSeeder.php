<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Containers\Files\Models\ImageType;

class ImageTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'logo'],
            ['name' => 'profile'],
            ['name' => 'cover'],
            ['name' => 'general'],
        ];
        foreach($types as $t) {
            ImageType::create($t);
        }
    }
}
