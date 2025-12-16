<?php

namespace Modules\WorkFromHome\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

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


    public function projects()
    {
        return $this->belongsToMany(ProjectCode::class, 'project_work_from_home', 'work_from_home_id', 'project_id')
            ->withPivot('deliverables')
            ->withTimestamps();
    }

    public function getProjectNames()
    {
        return $this->projects->pluck('short_name')->toArray();
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
}
