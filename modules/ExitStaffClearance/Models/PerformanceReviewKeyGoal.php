<?php

namespace Modules\ExitStaffClearance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\ExitStaffClearance\Models\StaffClearance;

use App\Traits\ModelEventLogger;

class PerformanceReviewKeyGoal extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'staff_clearance_key_goals';

    protected $fillable = [
        'staff_clearance_id',
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
        return $this->belongsTo(StaffClearance::class, 'staff_clearance_id');
    }
}