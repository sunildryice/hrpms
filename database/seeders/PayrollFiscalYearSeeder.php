<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\NepaliFiscalYear;

class PayrollFiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NepaliFiscalYear::updateOrCreate(
            ['id' => 1],
            [
                'title' => '2082/83',
                'start_date' => '2025-07-17',
                'end_date' => '2026-07-16',
                'activated_at' => now(),
            ]
        );

        NepaliFiscalYear::updateOrCreate(
            ['id' => 2],
            [
                'title' => '2083/84',
                'start_date' => '2026-07-17',
                'end_date' => '2027-07-16',
                'activated_at' => null,
            ]
        );
    }
}
