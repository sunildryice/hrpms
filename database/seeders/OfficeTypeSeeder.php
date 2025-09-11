<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\OfficeType;

class OfficeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $officeType = new OfficeType();
        $officeType->create([
            'id'=>1,
            'title'=>'head'
        ]);
        $officeType->create([
            'id'=>2,
            'title'=>'cluster'
        ]);
        $officeType->create([
            'id'=>3,
            'title'=>'district'
        ]);
    }
}
