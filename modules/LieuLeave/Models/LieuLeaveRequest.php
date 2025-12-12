<?php

namespace Modules\LieuLeave\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LieuLeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'lieu_leave_requests';

    protected $fillable = [
        'requester_id',
        'approver_id',
        'start_date',
        'end_date',
        'request_date',
        'reason',
        'status_id',
        'fiscal_year_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
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

    public function project()
    {
        return $this->belongsTo(ProjectCode::class);
    }

    public function logs()
    {
        return $this->hasMany(LieuLeaveRequestLog::class, 'lieu_leave_request_id');
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
        return 'LLR-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get all substitutes of leave request.
     */
    public function substitutes()
    {
        return $this->belongsToMany(
            Employee::class,                           // related model
            'lieu_leave_request_substitutes',      // pivot table
            'lieu_leave_request_id',               // this model FK
            'substitute_id'                              // related model FK
        );
    }

    public function leaveBalance()
    {
        return $this->hasOne(LieuLeaveBalance::class, 'lieu_leave_request_id');
    }
}
