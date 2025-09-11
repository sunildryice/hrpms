<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\BloodGroup;

class BloodGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group = new BloodGroup();
        $group->create([
            'id'=>1,
            'title' => 'A+',
        ]);
        $group->create([
            'id'=>2,
            'title' => 'A-',
        ]);
        $group->create([
            'id'=>3,
            'title' => 'B+',
        ]);
        $group->create([
            'id'=>4,
            'title' => 'B-',
        ]);
        $group->create([
            'id'=>5,
            'title' => 'AB+',
        ]);
        $group->create([
            'id'=>6,
            'title' => 'AB-',
        ]);
        $group->create([
            'id'=>7,
            'title' => 'O+',
        ]);
        $group->create([
            'id'=>8,
            'title' => 'O-',
        ]);
    }
}
