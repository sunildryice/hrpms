<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\BillCategory;

class BillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group = new BillCategory();
        $group->updateOrCreate([
            'id'=>1,
            'title' => 'Insurance'
        ], ['activated_at'=>date('Y-m-d H:i')]);
        $group->updateOrCreate([
            'id'=>2,
            'title' => 'Rent'
        ], ['activated_at'=>date('Y-m-d H:i')]);
        $group->updateOrCreate([
            'id'=>3,
            'title' => 'Misc'
        ], ['activated_at'=>date('Y-m-d H:i')]);
    }
}
