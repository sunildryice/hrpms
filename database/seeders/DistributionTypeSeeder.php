<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\DistributionType;

class DistributionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new DistributionType();
        $type->updateOrCreate([
            'id'=>1,
            'title' => 'Office Use',
        ]);
        $type->updateOrCreate([
            'id'=>2,
            'title' => 'Distribution',
        ]);
    }
}
