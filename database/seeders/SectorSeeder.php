<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            'Health System Strengthening',
            'Emergency Response and Recovery',
            'Maternal and Child Health',
            'Climate Change Livelihood and Health',
            'Disease Prevention and Management',
            'Health Policy and Systems Research',
            'Non-Communicable diseases',
            'Monitoring and Evaluation',
            'Nutrition'
        ];

        foreach ($sectors as $title) {
            DB::table('lkup_sectors')->updateOrInsert(
                ['title' => $title],
                [
                    'enable_field' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}