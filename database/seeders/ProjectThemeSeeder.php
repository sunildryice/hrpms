<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            'Research',
            'Implementation Project',
        ];

        foreach ($themes as $title) {
            DB::table('lkup_project_themes')->updateOrInsert(
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