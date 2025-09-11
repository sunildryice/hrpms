<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\EducationLevel;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $level = new EducationLevel();
        $level->create([
            'id'=>1,
            'title' => 'School Level',
        ]);
        $level->create([
            'id'=>2,
            'title' => 'SLC',
        ]);
        $level->create([
            'id'=>3,
            'title' => 'Intermediate or Equivalent',
        ]);
        $level->create([
            'id'=>4,
            'title' => "Bachelor's or Equivalent",
        ]);
        $level->create([
            'id'=>5,
            'title' => "Master's or Equivalent",
        ]);
        $level->create([
            'id'=>6,
            'title' => 'Doctorate or equivalent',
        ]);
    }
}
