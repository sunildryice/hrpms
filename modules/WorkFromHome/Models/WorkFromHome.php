<?php

namespace Modules\WorkFromHome\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectActivity;
use Modules\WorkFromHome\Models\WorkFromHomeLog;

class WorkFromHome extends Model
{
    use HasFactory;

    protected $table = 'work_from_homes';

    protected $fillable = [
        'start_date',
        'end_date',
        'request_date',
        'requester_id',
        'approver_id',
        'fiscal_year_id',
        'reason',
        'deliverables',
        'office_id',
        'department_id',
        'status_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'deliverables' => 'array',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'request_date',
    ];


    public function getProjectNames(): array
    {

        $deliverables = collect($this->deliverables ?? []);

        $projectIds = $deliverables
            ->pluck('project_id')
            ->unique()
            ->values()
            ->all();

        if (empty($projectIds)) {
            return [];
        }

        return Project::whereIn('id', $projectIds)
            ->pluck('short_name')
            ->values()
            ->all();
    }

    public function getActivityNames(): array
    {

        $deliverables = collect($this->deliverables ?? []);

        $activityIds = $deliverables
            ->pluck('activity_id')
            ->unique()
            ->values()
            ->all();

        if (empty($activityIds)) {
            return [];
        }

        return ProjectActivity::whereIn('id', $activityIds)
            ->pluck('title')
            ->values()
            ->all();
    }

    public function getDeliverablesWithProjectNames(): array
    {
        return collect($this->deliverables ?? [])
            ->map(function ($item, $projectId) {
                $project = Project::find($item['project_id']);
                $activity = ProjectActivity::find($item['activity_id']);

                return [
                    'project_id' => (int) $projectId,
                    'project_name' => $project ? $project->short_name : $project?->title,
                    'task' => $item['task'],
                    'activity_name' => $activity ? $activity->title : 'N/A',
                    'date' => isset($item['date']) ? Carbon::parse($item['date'])->format('M j, Y') : '',
                ];
            })
            ->values()
            ->all();
    }


    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }


    public function getStartDate()
    {
        return Carbon::parse($this->start_date)->format('M j, Y');
    }

    public function getEndDate()
    {
        return Carbon::parse($this->end_date)->format('M j, Y');
    }

    public function getRequestDate()
    {
        return Carbon::parse($this->request_date)->format('M j, Y');
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id', 'id', 'users')->withDefault();
    }

    public function logs()
    {
        return $this->hasMany(WorkFromHomeLog::class, 'work_from_home_id', 'id')->orderBy('created_at', 'desc');
    }
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getRequesterName()
    {

        return $this->requester->employee->getFullName();
    }


    public function getRequestId()
    {
        $workFromHomeNumber = $this->work_from_home_number ? 'WFH-' . $this->work_from_home_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';

        return $workFromHomeNumber . $fiscalYear;
    }


    public function getTotalDays()
    {
        return $this->end_date ? $this->end_date->diffInDays($this->start_date) + 1 : 1;
    }

    public function getWorkFromHomeDuration()
    {
        return ($this->end_date && $this->start_date) ? $this->end_date->diffInDays($this->start_date) + 1 : 0;
    }
}
