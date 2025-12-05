<?php

namespace Modules\WorkFromHome\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

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
        'project_id',
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


    public function project()
    {
        return $this->belongsTo(ProjectCode::class, 'project_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
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
        return 'WFH-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
