<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\VehicleRequestType;

class VehicleRequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicleRequestType = new VehicleRequestType();
        $vehicleRequestType->updateOrCreate(
            [
                'id'=>1,
                'title'=>'Office Vehicle'
            ],
        );
        $vehicleRequestType->updateOrCreate(
            [
                'id'=>2,
                'title'=>'Hire Vehicle'
            ],
        );
    }
}
