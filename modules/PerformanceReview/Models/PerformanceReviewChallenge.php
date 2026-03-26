<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReviewChallenge extends Model
{
    protected $table = 'performance_review_challenges';

    protected $fillable = [
        'performance_review_id',
        'challenge',
        'result',
        'created_by',
        'updated_by',
    ];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}