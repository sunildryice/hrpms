<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\FamilyRelation;

class FamilyRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $relation = new FamilyRelation();
        $relation->create([
            'id'=>1,
            'title' => 'Father',
        ]);
        $relation->create([
            'id'=>2,
            'title' => 'Mother',
        ]);
        $relation->create([
            'id'=>3,
            'title' => 'Grand Father',
        ]);
        $relation->create([
            'id'=>4,
            'title' => 'Grand Mother',
        ]);
        $relation->create([
            'id'=>5,
            'title' => 'Spouse',
        ]);
        $relation->create([
            'id'=>6,
            'title' => 'Son',
        ]);
        $relation->create([
            'id'=>7,
            'title' => 'Daughter',
        ]);
        $relation->create([
            'id'=>8,
            'title' => 'Uncle',
        ]);
        $relation->create([
            'id'=>9,
            'title' => 'Aunt',
        ]);
        $relation->create([
            'id'=>10,
            'title' => 'Friend',
        ]);
    }
}
