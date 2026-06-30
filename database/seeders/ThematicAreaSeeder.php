<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThematicAreaSeeder extends Seeder
{
    public function run(): void
    {
        $thematicAreas = [
            'AMR',
            'Climate Change',
            'Health Emergencies',
            'Family Planning',
            'Health Systems',
            'MNH',
            'Mental Health',
            'Digital Health',
            'Nutrition',
            'NCDs',
            'TB',
            'Urban Health',
            'Agriculture',
            'Others Specify',
        ];

        foreach ($thematicAreas as $title) {
            DB::table('lkup_thematic_areas')->updateOrInsert(
                ['title' => $title],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
