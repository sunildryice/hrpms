<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\ProbationaryReviewType;

class ProbationaryReviewTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $review = new ProbationaryReviewType();
        $review->updateOrCreate([
            'id'=>1,
            'title' => 'Initial Meeting',
        ]);
        $review->updateOrCreate([
            'id'=>2,
            'title' => 'First Review',
        ]);
        $review->updateOrCreate([
            'id'=>3,
            'title' => 'Second Review',
        ]);
    }
}
