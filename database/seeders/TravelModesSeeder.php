<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\TravelMode;

class TravelModesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $travelMode = new TravelMode();
        $travelMode->updateOrCreate([
            'id' => '1'
        ],[
            'title' => 'Air',
        ]);
        $travelMode->updateOrCreate([
            'id' => '2'
        ],[
            'title' => 'Road',
        ]);
        $travelMode->updateOrCreate([
            'id' => '6'
        ],[
            'title' => 'Walking',
        ]);
        $travelMode->updateOrCreate([
            'id' => '7'
        ],[
            'title' => 'Others'
        ]);
    }
}
