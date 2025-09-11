<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\FiscalYear;

class FiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = new FiscalYear();
        $year->updateOrCreate([
            'id'=>1],
            [
            'title' => '2022',
            'start_date' => '2022-01-01',
            'end_date' => '2022-12-31',
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $year->updateOrCreate([
            'id'=>2],[
            'title' => '2023',
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31',
            'activated_at' => NULL,
        ]);

        $year->updateOrCreate([
            'id' => 3
            ], [
            'title' => '2024',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'activated_at' => NULL,
        ]);
        $year->updateOrCreate([
            'id' => 4
            ], [
            'title' => '2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'activated_at' => NULL,
        ]);
    }
}
