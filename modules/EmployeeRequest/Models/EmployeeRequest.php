<?php

namespace Modules\EmployeeRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\District;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\EmployeeType;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class EmployeeRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_requisitions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'employee_type_id',
        'office_id',
        'duty_station_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'position_title',
        'position_level',
        'replacement_for',
        'requested_date',
        'required_date',
        'budgeted',
        'work_load',
        'duration',
        'reason_for_request',
        'employee_type_other',
        'education_required',
        'education_preferred',
        'experience_required',
        'experience_preferred',
        'skills_required',
        'skills_preferred',
        'other_required',
        'other_preferred',
        'logistics_requirement',
        'tor_jd_submitted',
        'tentative_submission_date',
        'tor_jd_attachment',
        'reviewer_id',
        'approver_id',
        'status_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [];

    /**
     * Get the account of the employee request.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activity of the employee request.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the approver of a employee request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the donor of the employee request.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the dutyStation of the employee request.
     */
    public function dutyStation()
    {
        return $this->belongsTo(District::class, 'duty_station_id')->withDefault();
    }

    /**
     * Get the employeeType of the employee request.
     */
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    /**
     * Get the fiscalYear of the employee request.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the employee request.
     */
    public function logs()
    {
        return $this->hasMany(EmployeeRequestLog::class, 'employee_requisition_id');
    }

    public function submittedLog()
    {
        return $this->hasOne(EmployeeRequestLog::class, 'employee_requisition_id')
        ->where('status_id', config('constant.SUBMITTED_STATUS'))
        ->latest()
        ->withDefault();
    }

    public function reviewedLog()
    {
        return $this->hasOne(EmployeeRequestLog::class, 'employee_requisition_id')
        ->where('status_id', config('constant.VERIFIED_STATUS'))
        ->latest()
        ->withDefault();
    }

    public function recommendedLog()
    {
        return $this->hasOne(EmployeeRequestLog::class, 'employee_requisition_id')
        ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')])
        ->latest()
        ->withDefault();
    }

    public function approvedLog()
    {
        return $this->hasOne(EmployeeRequestLog::class, 'employee_requisition_id')
        ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
        ->latest()
        ->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get requester of the employee request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get reviewer of the employee request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the employee request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getDutyStation()
    {
        return $this->dutyStation->getDistrictName();
    }

    public function getEmployeeType()
    {
        return $this->employeeType ? $this->employeeType->title : 'Other';
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->getFiscalYear();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getApprovedDate()
    {
        return $this->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at->toFormattedDateString();
    }
}
