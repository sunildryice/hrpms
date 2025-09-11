<?php

namespace Modules\WorkLog\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityArea;
use Modules\Master\Models\Priority; use Modules\EmployeeAttendance\Models\AttendanceDetailDonor;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\ProjectCode;

class WorkPlanDailyLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'work_plan_daily_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_plan_id',
        'activity_area_id',
        'priority_id',
        'log_date',
        'major_activities',
        'status',
        'other_activities',
        'remarks',
        'project_id',
        'donor_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the daily work logs.
     */
    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    // public function attendanceDetailDonor()
    // {
    //     return $this->belongsTo(AttendanceDetailDonor::class, 'att_detail_donor_id')->withDefault();
    // }

    /**
     * Get the activity area.
     */
    public function activityArea()
    {
        return $this->belongsTo(ActivityArea::class, 'activity_area_id')->withDefault();
    }

    public function donor()
    {
        return $this->belongsTo(DonorCode::class, 'donor_id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(ProjectCode::class, 'project_id', 'id')->withDefault();
    }

    /**
     * Get the priority.
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id')->withDefault();
    }

    public function getActivityArea()
    {
        return $this->activityArea->title;
    }

    public function getPriority()
    {
        return $this->priority->title;
    }
}
