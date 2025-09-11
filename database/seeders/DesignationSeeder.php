<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Designation;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designation = new Designation();
        $designation->create([
            'id'=>1,
            'title' => 'National Director',
        ]);
        $designation->create([
            'id'=>2,
            'title' => 'Director',
        ]);
        $designation->create([
            'id'=>3,
            'title' => 'Sr. Officer',
        ]);
        $designation->create([
            'id'=>4,
            'title' => 'Officer',
        ]);
        $designation->create([
            'id'=>5,
            'title' => 'Jr. Officer',
        ]);
    }
}
