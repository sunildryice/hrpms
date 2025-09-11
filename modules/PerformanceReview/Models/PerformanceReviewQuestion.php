<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\PerformanceREview\Models\PerformanceReviewAnswer;

use App\Traits\ModelEventLogger;

class PerformanceReviewQuestion extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_performance_review_questions';

    protected $fillable = [
        'question',
        'answer_type',
        'description',
        'activated_at',
        'position',
        'group',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    protected $dates = ['activated_at'];

    public function answer()
    {
        return $this->hasMany(PerformanceReviewAnswer::class, 'question_id');
    }
}