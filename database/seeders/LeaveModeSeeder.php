<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\LeaveMode;

class LeaveModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveMode = new LeaveMode();
        $leaveMode->updateOrCreate(
            ['title' => 'Full Day'],
            ['hours' => 8],
        );
        $leaveMode->updateOrCreate(
            ['title' => 'First Half'],
            ['hours' => 4],
        );
        $leaveMode->updateOrCreate(
            ['title' => 'Second Half'],
            ['hours' => 4],
        );
        $leaveMode->updateOrCreate(
            ['title' => '2 Hour'],
            ['hours' => 2],
        );
        $leaveMode->updateOrCreate(
            ['title' => 'No Leave'],
            ['hours' => 0],
        );
    }
}
