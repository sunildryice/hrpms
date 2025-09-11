<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\MaritalStatus;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = new MaritalStatus();
        $status->create([
            'id'=>1,
            'title' => 'Single',
        ]);
        $status->create([
            'id'=>2,
            'title' => 'Married',
        ]);
    }
}
