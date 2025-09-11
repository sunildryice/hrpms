<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\DsaCategory;

class DsaCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dsaCategory = new DsaCategory();
        $dsaCategory->create([
            'id'=>1,
            'title' => 'G1',
        ]);
        $dsaCategory->create([
            'id'=>2,
            'title' => 'G2',
        ]);
    }
}
