<?php

namespace Modules\LeaveRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\LeaveRequest\Models\LeaveEncashLog;
use Modules\Master\Models\Department;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LeaveType;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LeaveEncash extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leave_encash_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'office_id',
        'department_id',
        'leave_type_id',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'fiscal_year_id',
        'available_balance',
        'encash_balance',
        'prefix',
        'encash_number',
        'modification_number',
        'modification_leave_encash_id',
        'request_date',
        'remarks',
        'status_id',
        'pay_date',
        'paid_at',
        'payment_remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['request_date', 'pay_date', 'paid_at'];

    /**
     * Get the approved log for the leave encash.
     */
    public function approvedLog()
    {
        return $this->hasOne(LeaveEncashLog::class, 'leave_encash_id')
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    /**
     * Get modified leave encash of a leave encash
     */
    public function childLeaveEncash()
    {
        return $this->hasOne(LeaveEncash::class, 'modification_leave_encash_id');
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
     * Get the leave type of the leave encash.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id')->withDefault();
    }

    /**
     * Get the logs for the leave encash.
     */
    public function logs()
    {
        return $this->hasMany(LeaveEncashLog::class, 'leave_encash_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get parent of the modified leave encash.
     */
    public function parentLeaveEncash()
    {
        return $this->belongsTo(LeaveEncash::class, 'modification_leave_request_id');
    }

    /**
     * Get requester of the leave encash.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the reviewed log for the leave encash.
     */
    public function reviewedLog()
    {
        return $this->hasOne(LeaveEncashLog::class, 'leave_encash_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    /**
     * Get reviewer of the leave encash.
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
     * Get the submitted log for the leave encash.
     */
    public function submittedLog()
    {
        return $this->hasOne(LeaveEncashLog::class, 'leave_encash_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    public function paidLog()
    {
        return $this->hasOne(LeaveEncashLog::class, 'leave_encash_id')
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->latest();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getPayerName()
    {
        return $this->paidLog()?->first()->createdBy->getFullName();
    }

    public function getPayerDesignation()
    {
        return $this->paidLog()?->first()->createdBy->employee->designation->title;
    }

    public function getLeaveDuration()
    {
        return $this->leaveType->leave_basis == 2 ? $this->leaveDays->sum('leave_duration') : $this->leaveDays->sum('leave_duration') / 8;
    }

    public function getEncashNumber()
    {
        $encashNumber = $this->prefix . '-' . $this->encash_number;
        $encashNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        return $encashNumber;
    }

    public function getLeaveType()
    {
        return ucwords($this->leaveType->title);
    }

    public function getPaymentDate()
    {
        return $this->pay_date->toFormattedDateString();
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : "";
    }

    public function getRequestDate()
    {
        return $this->request_date->toFormattedDateString();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getRequestMonth()
    {
        return $this->request_date->format('F');
    }
}
