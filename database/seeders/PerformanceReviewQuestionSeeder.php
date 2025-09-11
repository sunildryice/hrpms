<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;

class PerformanceReviewQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new PerformanceReviewQuestion();
        $type->create([
            'id'            => 1,
            'question'      => 'List major achievements including new or increased responsibilities:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'B'
        ]);
        $type->create([
            'id'            => 2,
            'question'      => 'List any major challenges and difficulties you faced:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 2,
            'group'         => 'B'
        ]);
        $type->create([
            'id'            => 3,
            'question'      => 'Communication/working relationships:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'D'
        ]);
        $type->create([
            'id'            => 4,
            'question'      => 'Productivity/ job effectiveness/ organization & planning:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 2,
            'group'         => 'D'
        ]);
        $type->create([
            'id'            => 5,
            'question'      => 'Leadership/ developing others:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 3,
            'group'         => 'D'
        ]);
        $type->create([
            'id'            => 6,
            'question'      => 'Problem solving/ judgement/ analytics:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 4,
            'group'         => 'D'
        ]);
        $type->create([
            'id'            => 7,
            'question'      => 'Accountability:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 5,
            'group'         => 'D'
        ]);
        $type->create([
            'id'            => 8,
            'question'      => 'Identify strengths/critical accomplishments:',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'E'
        ]);
        $type->create([
            'id'            => 9,
            'question'      => 'Identify areas for growth/improvement: (List professional development plan with timeframe)',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 2,
            'group'         => 'E'
        ]);
        $type->create([
            'id'            => 10,
            'question'      => 'Professional Development Plan',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 3,
            'group'         => 'E'
        ]);
        $type->create([
            'id'            => 11,
            'question'      => 'Substantially exceeds expectations',
            'answer_type'   => 'boolean',
            'description'   => 'Has mastered all job-related skills and consistently delivers outstanding results in all areas of responsibility.',
            'position'      => 1,
            'group'         => 'F'
        ]);
        $type->create([
            'id'            => 12,
            'question'      => 'Exceeds expectations',
            'answer_type'   => 'boolean',
            'description'   => 'Highly skilled in relation to technical requirements of the job and regularly produces above-average results in all areas of responsibility.',
            'position'      => 2,
            'group'         => 'F'
        ]);
        $type->create([
            'id'            => 13,
            'question'      => 'Meets all expectations',
            'answer_type'   => 'boolean',
            'description'   => '',
            'position'      => 3,
            'group'         => 'F'
        ]);
        $type->create([
            'id'            => 14,
            'question'      => 'Partially meets expectations',
            'answer_type'   => 'boolean',
            'description'   => 'Demonstrates beginner knowledge / skill level and does not yet consistently deliver expected results in all areas of responsibility. Needs improvement.',
            'position'      => 4,
            'group'         => 'F'
        ]);
        $type->create([
            'id'            => 15,
            'question'      => 'Below expectations',
            'answer_type'   => 'boolean',
            'description'   => 'Demonstrates insufficient skills and does not deliver expected results in all areas of responsibility. Significant and immediate improvement required.',
            'position'      => 5,
            'group'         => 'F'
        ]);
        $type->create([
            'id'            => 16,
            'question'      => 'Identify At Least 3 Key Goals For The Next Review Period: (to be completed jointly)',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'G'
        ]);
        $type->create([
            'id'            => 17,
            'question'      => 'Employee Comments (optional) ',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'H'
        ]);
        $type->create([
            'id'            => 18,
            'question'      => 'Supervisor/ Next Line Manager Comments (optional)',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'I'
        ]);
        $type->create([
            'id'            => 19,
            'question'      => 'Acknowledgements',
            'answer_type'   => 'textarea',
            'description'   => '',
            'position'      => 1,
            'group'         => 'J'
        ]);
        
       
    }
}
