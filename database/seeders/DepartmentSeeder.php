<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $department = new Department();
        $department->create([
            'id'=>1,
            'title' => 'Admin',
        ]);
        $department->create([
            'id'=>2,
            'title' => 'Human Resource',
        ]);
        $department->create([
            'id'=>3,
            'title' => 'Procurement',
        ]);
        $department->create([
            'id'=>4,
            'title' => 'ICT',
        ]);
        $department->create([
            'id'=>5,
            'title' => 'Finance',
        ]);
    }
}
