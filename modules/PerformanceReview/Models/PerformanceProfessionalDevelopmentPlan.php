<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceProfessionalDevelopmentPlan extends Model
{
    protected $table = 'performance_professional_development_plans';

    protected $fillable = [
        'performance_review_id',
        'objective',
        'activity',
        'created_by',
        'updated_by',
    ];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}