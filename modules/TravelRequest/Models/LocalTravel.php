<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LocalTravel extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'local_travel_reimbursements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'employee_id',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'office_id',
        'fiscal_year_id',
        'prefix',
        'local_travel_number',
        'title',
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

    protected $dates = ['pay_date', 'paid_at'];

    /**
     * Get the approved log for the local travel.
     */
    public function approvedLog()
    {
        return $this->hasOne(LocalTravelLog::class, 'local_travel_reimbursement_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalyear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the office of the local travel
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullname();
    }

    /**
     * Get the requester of the local travel
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the recommended log for the local travel request.
     */
    public function recommendedLog()
    {
        return $this->hasOne(LocalTravelLog::class, 'local_travel_reimbursement_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the logs for the travel request.
     */
    public function logs()
    {
        return $this->hasMany(LocalTravelLog::class, 'local_travel_reimbursement_id');
    }

    /**
     * Get the logs for the travel request itinerary.
     */
    public function localTravelItineraries()
    {
        return $this->hasMany(LocalTravelItinerary::class, 'local_travel_reimbursement_id');
    }

    /**
     * Get the travel status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the local travel.
     */
    public function submittedLog()
    {
        return $this->hasOne(LocalTravelLog::class, 'local_travel_reimbursement_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    public function getSubmittedDate()
    {
        return $this->submittedLog?->created_at->toFormattedDateString();
    }
    /**
     * Get the travel request for the local travel reimbursement.
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getLocalTravelNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->prefix.$this->local_travel_number.$fiscalYear;
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getRequesterName()
    {
        return $this->requester->full_name;
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getTravelRequestNumber()
    {
        return $this->travelRequest ? $this->travelRequest->getTravelRequestNumber() : '';
    }

    public function getPaymentDate()
    {
        return $this->pay_date->toFormattedDateString();
    }

    public function getPayerName()
    {
        return $this->paidLog()?->first()->createdBy->getFullName();
    }

    public function getPayerDesignation()
    {
        return $this->paidLog()?->first()->createdBy->employee->designation->title;
    }

    public function paidLog()
    {
        return $this->hasOne(LocalTravelLog::class, 'local_travel_reimbursement_id')
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->latest();
    }

    public function isConsultantTravel()
    {
        return $this->requester->employee_id != $this->employee_id && isset($this->employee_id);
    }

    public function getTraveller()
    {
        return $this->isConsultantTravel() ? $this->employee : $this->requester->employee;
    }

    public function getTravellerName()
    {
        return $this->getTraveller()->getFullName();
    }

    public function getTravellerDesignation()
    {
        return $this->getTraveller()->getDesignationName();
    }

    public function getTravellerAddress()
    {
        return $this->getTraveller()->address->getPermanentAddress();
    }

    public function getTravellerDepartment()
    {
        return $this->getTraveller()->getDepartmentName();
    }

    public function getTravellerDutyStation()
    {
        return $this->getTraveller()->getDutyStation();
    }

    public function getTravellerPhone()
    {
        return $this->getTraveller()->mobile_number;
    }
}
