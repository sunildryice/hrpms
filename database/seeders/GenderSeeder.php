<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gender = new Gender();
        $gender->create([
            'id'=>1,
            'title' => 'Male',
        ]);
        $gender->create([
            'id'=>2,
            'title' => 'Female',
        ]);
        $gender->create([
            'id'=>3,
            'title' => 'Other',
        ]);
    }
}
