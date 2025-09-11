<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\MeetingHall;

class MeetingHallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $meetingHall = new MeetingHall();
        $meetingHall->create([
            'id'=>1,
            'title' => 'Meeting Hall 1',
        ]);
        $meetingHall->create([
            'id'=>2,
            'title' => 'Meeting Hall 2',
        ]);
    }
}
