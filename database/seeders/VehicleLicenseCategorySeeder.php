<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleLicenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'K', 'name' => 'Scooter / Moped', 'description' => 'Two-wheeler up to 50cc or equivalent electric power'],
            ['code' => 'A', 'name' => 'Motorcycle', 'description' => 'Motorcycles above 50cc (all engine capacities)'],
            ['code' => 'B', 'name' => 'Car / Jeep / Van', 'description' => 'Light motor vehicles (up to 14 seats including driver)'],
            ['code' => 'C', 'name' => 'Tempo / Auto Rickshaw', 'description' => 'Three-wheeled passenger vehicles'],
            ['code' => 'D', 'name' => 'Power Tiller', 'description' => 'Agricultural power tiller'],
            ['code' => 'E', 'name' => 'Tractor', 'description' => 'Agricultural tractor'],
            ['code' => 'F', 'name' => 'Heavy Vehicle (Bus / Truck)', 'description' => 'Heavy passenger buses and goods carriers'],
            ['code' => 'G', 'name' => 'Heavy Equipment (Crane, Dozer, Excavator, Loader)', 'description' => 'Construction and earth-moving machinery'],
            ['code' => 'H', 'name' => 'Minibus / Minitruck', 'description' => 'Medium-sized passenger and goods vehicles'],
        ];

        foreach ($categories as $cat) {
            DB::table('lkup_vehicle_license_categories')->updateOrInsert(
                ['code' => $cat['code']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}