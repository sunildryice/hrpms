<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicleType = new VehicleType();
        $vehicleType->updateOrCreate(
            ['title'=>'Car'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'Pick up Jeep'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'SML Truck'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'4WD Jeep Scorpio'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'4WD Bolero'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'Mini Truck'],
        );
        $vehicleType->updateOrCreate(
            ['title'=>'4WD Jeep Toyota/Prado/Land Cruiser'],
        );
    }
}
