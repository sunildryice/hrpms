<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Models\DispositionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DispositionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new DispositionType();

        $type->updateOrCreate([
            'id' => 1,
        ], [
            'title' => 'Dispose'
        ]);

        $type->updateOrCreate([
            'id' => 2,
        ], [
            'title' => 'Lost'
        ]);

        $type->updateOrCreate([
            'id' => 3,
        ], [
            'title' => 'Handover of Assets'
        ]);

        $type->updateOrCreate([
            'id' => 4,
        ], [
            'title' => 'Sell Asset Disposition'
        ]);
    }
}
