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
        TravelMode::truncate();
        $travelMode = new TravelMode();
        $travelMode->updateOrCreate([
            'id' => '1'
        ], [
            'title' => 'Air',
        ]);
        $travelMode->updateOrCreate([
            'id' => '2'
        ], [
            'title' => 'Road (Rental)',
        ]);
        $travelMode->updateOrCreate([
            'id' => '3'
        ], [
            'title' => 'Road (Public Transport)',
        ]);
    }
}
