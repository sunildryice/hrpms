<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\TravelType;

class TravelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $travelType = new TravelType();
        $travelType->create([
            'id'=>1,
            'title' => 'National',
        ]);
        $travelType->create([
            'id'=>2,
            'title' => 'International',
        ]);
    }
}
