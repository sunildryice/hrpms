<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\InventoryType;

class InventoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new InventoryType();
        $type->updateOrCreate(
            ['title'=>'Consumable'],
        );
        $type->updateOrCreate(
            ['title'=>'Non Consumable'],
        );
    }
}
