<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Carbon\Carbon;

class WorkPlan extends Model
{
    protected $table = 'work_plan';

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function getRowClassAttribute()
    {
        $now = Carbon::now()->startOfDay();

        if ($now->between($this->from_date, $this->to_date)) {
            return 'current-week';
        }

        if ($this->to_date->lt($now)) {
            return 'past-week';
        }

        return 'future-week';
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(WorkPlanDetail::class);
    }

    public function projects()
    {
        return $this->hasManyThrough(
            Project::class,
            WorkPlanDetail::class,
            'work_plan_id',
            'id',
            'id',
            'project_id'
        );
    }
}
