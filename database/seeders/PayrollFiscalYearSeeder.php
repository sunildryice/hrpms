<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Payroll\Models\PayrollFiscalYear;

class PayrollFiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = new PayrollFiscalYear();
        $year->create([
            'id'=>1,
            'title' => '2079/80',
            'start_date' => '2022-07-01',
            'end_date' => '2023-06-30',
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $year->create([
            'id'=>2,
            'title' => '2080/81',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'activated_at' => NULL,
        ]);
    }
}
