<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\PerformanceREview\Models\PerformanceReview;
use Modules\PerformanceREview\Models\PerformanceReviewQuestion;

use App\Traits\ModelEventLogger;

class PerformanceReviewAnswer extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'performance_review_answers';

    protected $fillable = [
        'performance_review_id',
        'question_id',
        'answer',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    protected $dates = [];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id')->withDefault();
    }

    public function performanceReviewQuestion()
    {
        return $this->belongsTo(PerformanceReviewQuestion::class, 'question_id');
    }
}