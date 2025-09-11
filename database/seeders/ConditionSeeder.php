<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Condition;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeder
     * @return void
     */
    public function run()
    {
        $condition = new Condition();

        $condition->create([
            'title' => 'Good'
        ]);

        $condition->create([
            'title' => 'Poor'
        ]);

        $condition->create([
            'title' => 'Damage'
        ]);
    }
}