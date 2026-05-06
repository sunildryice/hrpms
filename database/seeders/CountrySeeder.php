<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            'Nepal',
            'India',
            'Bangladesh',
            'Pakistan',
            'United Kingdom',
            'United States',
            'Sri Lanka',
        ];

        foreach ($countries as $title) {
            DB::table('lkup_countries')->insertOrIgnore([
                'title' => $title,
                'enable_field' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
