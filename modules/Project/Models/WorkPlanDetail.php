<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPlanDetail extends Model
{
    protected $table = 'work_plan_details';

    protected $fillable = [
        'work_plan_id',
        'project_id',
        'project_activity_id',
        'plan_tasks',
        'status',
        'reason',
    ];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'project_activity_id');
    }
}
