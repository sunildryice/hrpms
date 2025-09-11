<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Priority;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $priority = new Priority();
        $priority->create([
            'id'=>1,
            'title' => 'High',
        ]);
        $priority->create([
            'id'=>2,
            'title' => 'Intermediate',
        ]);
        $priority->create([
            'id'=>3,
            'title' => 'Low',
        ]);
    }
}
