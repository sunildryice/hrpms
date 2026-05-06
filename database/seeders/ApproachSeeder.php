<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApproachSeeder extends Seeder
{
    public function run(): void
    {
        $approaches = [
            'Qualitative',
            'Randomized Control Trials',
            'Mixed Method Study',
            'Cross Sectional Study',
            'Survey',
            'Census',
            'Toolkit Development',
            'Systematic Review',
            'Review',
            'Documentation',
            'Technical Assistance'
        ];

        foreach ($approaches as $title) {
            DB::table('lkup_approaches')->updateOrInsert(
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