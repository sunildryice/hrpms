<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\EmployeeType;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeType = new EmployeeType();
        $employeeType->updateOrCreate([
            'title' => 'Full Time Employee',
        ]);
        $employeeType->updateOrCreate([
            'title' => 'Full Time Consultant',
        ]);
        $employeeType->updateOrCreate([
            'title' => 'Part Time',
        ]);
        $employeeType->updateOrCreate([
            'title' => 'Time Based',
        ]);
        $employeeType->updateOrCreate([
            'title' => 'Task Based',
        ]);
    }
}
