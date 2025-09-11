<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\PerformanceReview\Models\PerformanceReview;

use App\Traits\ModelEventLogger;

class PerformanceReviewKeyGoal extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'performance_review_key_goals';

    protected $fillable = [
        'performance_review_id',
        'title',
        'description_employee',
        'description_supervisor',
        'description_employee_annual',
        'description_supervisor_annual',
        'type',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    protected $dates = ['created_at', 'updated_at'];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}