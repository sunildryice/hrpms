<?php

namespace Modules\OffDayWork\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;


class OffDayWork extends Model
{
    use HasFactory;

    protected $table = 'off_day_works';

    protected $fillable = [
        'requester_id',
        'approver_id',
        'date',
        'deliverables',
        'fiscal_year_id',
        'reason',
        'status_id',
    ];

    protected $casts = [
        'date' => 'date',
        'deliverables' => 'array',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // public function projects(): EloquentCollection
    // {
    //     $deliverables = collect($this->deliverables ?? []);

    //     $projectIds = $deliverables
    //         ->pluck('project_id')
    //         ->unique()
    //         ->values()
    //         ->all();

    //     return ProjectCode::whereIn('id', $projectIds)->get();
    // }


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

        return ProjectCode::whereIn('id', $projectIds)
            ->pluck('short_name')
            ->values()
            ->all();
    }

    public function getDeliverablesWithProjectNames(): array
    {
        return collect($this->deliverables ?? [])
            ->groupBy('project_id')
            ->map(function ($items, $projectId) {
                $project = ProjectCode::find($projectId);

                return [
                    'project_id'   => (int) $projectId,
                    'project_name' => $project ? $project->short_name : 'N/A',
                    'tasks'        => $items->pluck('task')->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function logs()
    {
        return $this->hasMany(OffDayWorkLog::class, 'off_day_work_id');
    }

    public function getOffDayWorkDate()
    {
        return Carbon::parse($this->date)->format('M j, Y');
    }


    public function getRequestDate()
    {
        return Carbon::parse($this->request_date)->format('M j, Y');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {

        return $this->status->status_class;
    }


    public function getRequesterName()
    {

        return $this->requester->employee->getFullName();
    }

    public function getRequestId()
    {
        $offDayWorkNumber = $this->off_day_work_number ? 'ODW-' . $this->off_day_work_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';

        return $offDayWorkNumber . $fiscalYear;
    }
}
