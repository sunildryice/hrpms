<?php

namespace Modules\PerformanceReview\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PerformanceReview\Models\Enums\KeyGoalStatus;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewKeyGoal extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'performance_review_key_goals';

    protected $fillable = [
        'performance_review_id',
        'title',
        'output_deliverables',
        'major_activities_employee',
        'status',
        'remarks_employee',
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
    protected $casts = [
        'status' => KeyGoalStatus::class,
    ];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
    public function getStatusLabelAttribute()
    {
        return $this->status?->label() ?? 'Not Set';
    }

    public function getStatusColorAttribute()
    {
        return $this->status?->colorClass() ?? 'badge bg-secondary';
    }
}