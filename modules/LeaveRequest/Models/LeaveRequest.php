<?php

namespace Modules\LeaveRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Department;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LeaveType;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LeaveRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leave_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_id',
        'department_id',
        'leave_type_id',
        'requester_id',
        'reviewer_id',
        'verifier_id',
        'approver_id',
        'fiscal_year_id',
        'prefix',
        'leave_number',
        'modification_number',
        'modification_leave_request_id',
        'start_date',
        'end_date',
        'request_date',
        'remarks',
        'review_remarks',
        'status_id',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['start_date', 'end_date', 'request_date'];

    /**
     * Get the approved log for the leave request.
     */
    public function approvedLog()
    {
        return $this->hasOne(LeaveRequestLog::class, 'leave_request_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver of a leave
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function hrReviewer()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    /**
     * Get modified leave request of a leave request
     */
    public function childLeaveRequest()
    {
        return $this->hasOne(LeaveRequest::class, 'modification_leave_request_id');
    }

    /**
     * Get the department of the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the leave type of the leave request.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id')->withDefault();
    }

    /**
     * Get the logs for the leave request.
     */
    public function logs()
    {
        return $this->hasMany(LeaveRequestLog::class, 'leave_request_id')
            ->orderBy('created_at', 'desc');
    }

    public function returnLog()
    {
        return $this->hasOne(LeaveRequestLog::class, 'leave_request_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get the leave days for the leave request.
     */
    public function leaveDays()
    {
        return $this->hasMany(LeaveRequestDay::class, 'leave_request_id');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get parent of the modified leave request.
     */
    public function parentLeaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'modification_leave_request_id');
    }

    /**
     * Get requester of the leave request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the reviewed log for the leave request.
     */
    public function reviewedLog()
    {
        return $this->hasOne(LeaveRequestLog::class, 'leave_request_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    public function verifiedLog()
    {
        return $this->hasOne(LeaveRequestLog::class, 'leave_request_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get reviewer of the leave request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the leave status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the leave request.
     */
    public function submittedLog()
    {
        return $this->hasOne(LeaveRequestLog::class, 'leave_request_id')
            ->where('user_id', $this->requester_id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.VERIFIED_STATUS')])
            ->latest();
    }

    /**
     * Get all substitutes of leave request.
     */
    public function substitutes()
    {
        return $this->belongsToMany(Employee::class, 'leave_request_substitutes', 'leave_request_id', 'substitute_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getReviewerName()
    {
        return $this->hrReviewer->getFullName();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
    }

    public function getLeaveDifferenceInDays()
    {
        return ($this->end_date && $this->start_date) ? $this->end_date->diffInDays($this->start_date) + 1 : 0;
    }

    public function getLeaveDuration()
    {
        return $this->leaveType->leave_basis == 2 ? $this->leaveDays->sum('leave_duration') : $this->leaveDays->sum('leave_duration') / 8;
    }

    public function getLeaveNumber()
    {
        $leaveNumber = $this->prefix.'-'.$this->leave_number;
        $leaveNumber .= $this->modification_number ? '-'.$this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $leaveNumber.$fiscalYear;
    }

    public function getLeaveType()
    {
        return ucwords($this->leaveType->title);
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getRequestDate()
    {
        return $this->request_date->toFormattedDateString();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getSubstitutes()
    {
        return $this->substitutes ? implode(', ', $this->substitutes->pluck('full_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    public function getStartDate()
    {
        return $this->start_date->toFormattedDateString();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getFirstLeaveTime(): ?string
    {
        $leaveDay = $this->leaveDays()->whereNotNull('leave_remarks')
            ->whereRaw("leave_remarks REGEXP '^[0-9]{2}:[0-9]{2}:[0-9]{2} - [0-9]{2}:[0-9]{2}:[0-9]{2}$'")
            ->where('leave_request_id', $this->id)
            ->where('leave_mode_id', '<>', 15)
            ->first();
        if (! $leaveDay) {
            $leaveDay = $this->leaveDays()
                // ->orWhere('leave_duration', 4)
                ->where('leave_request_id', $this->id) // should not be required but added due to bug
                ->where('leave_mode_id', '<>', '15')
                ->first();
        }

        if ($leaveDay?->leave_duration == 2) {
            return $leaveDay?->getLeaveTime();
        } else {
            return $leaveDay?->getLeaveMode();
        }
    }
}
