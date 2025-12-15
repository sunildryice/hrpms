<?php

namespace Modules\LieuLeave\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Termwind\Components\Li;

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

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
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

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getSubstitutes()
    {
        return $this->substitutes ? implode(', ', $this->substitutes->pluck('full_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }


    public function getRequesterName()
    {

        return $this->requester->employee->getFullName();
    }



    public function getApproverName()
    {
        return $this->approver->full_name ?? '-';
    }


    public function approvedLog()
    {
        return $this->hasOne(LieuLeaveRequestLog::class, 'lieu_leave_request_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }


    public function getRequestId()
    {
        $lieuLeaveRequestNumber =  $this->lieu_leave_request_number ? 'LLR-' . $this->lieu_leave_request_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';

        return $lieuLeaveRequestNumber . $fiscalYear;
    }

    public function submittedLog()
    {
        return $this->hasOne(LieuLeaveRequestLog::class, 'lieu_leave_request_id')
            ->where('user_id', $this->requester_id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])
            ->latest();
    }


    public function substitutes()
    {
        return $this->belongsToMany(
            Employee::class,
            'lieu_leave_request_substitutes',
            'lieu_leave_request_id',
            'substitute_id'
        );
    }

    public function leaveBalance()
    {
        return $this->hasOne(LieuLeaveBalance::class, 'lieu_leave_request_id');
    }
}
