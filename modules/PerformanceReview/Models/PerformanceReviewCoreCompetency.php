<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReviewCoreCompetency extends Model
{
    protected $table = 'performance_review_core_competencies';

    protected $fillable = [
        'performance_review_id',
        'competency',
        'rating',
        'example',
        'created_by',
        'updated_by',
    ];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}