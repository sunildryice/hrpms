<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\PerformanceReview\Models\PerformanceReviewType;

class PerformanceReviewTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new PerformanceReviewType();
        $type->create([
            'id'=>1,
            'title' => 'Annual Review',
        ]);
        $type->create([
            'id'=>2,
            'title' => 'Mid-Term Review',
        ]);
        $type->create([
            'id'=>3,
            'title' => 'Key Goals Review',
        ]);
    }
}
