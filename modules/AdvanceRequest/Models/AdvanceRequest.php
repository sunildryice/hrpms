<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class AdvanceRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'requester_id',
        'verifier_id',
        'reviewer_id',
        'approver_id',
        'fiscal_year_id',
        'district_id',
        'office_id',
        'request_for_office_id',
        'project_code_id',
        'prefix',
        'advance_number',
        'required_date',
        'request_date',
        'start_date',
        'end_date',
        'settlement_date',
        'purpose',
        'outstandinkg_advance',
        'status_id',
        'pay_date',
        'paid_at',
        'payment_remarks',
        'close_remarks',
        'closed_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['required_date', 'request_date', 'start_date', 'end_date', 'settlement_date', 'closed_at'];

    /**
     * Get the advance items for the advance request.
     */
    public function advanceRequestDetails()
    {
        return $this->hasMany(AdvanceRequestDetail::class, 'advance_request_id');
    }

    /**
     * Get the advance settlement
     */
    public function advanceSettlement()
    {
        return $this->hasOne(Settlement::class, 'advance_request_id');
    }

    /**
     * Get the approver of a advance
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the district of the employee.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

     /**
     * Get the project codes.
     */
    public function projectCodes()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the requested for office.
     */
    public function requestForOffice()
    {
        return $this->belongsTo(Office::class, 'request_for_office_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get requester of the advance request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get reviewer of the advance request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the recommended log for the advance request.
     */
    public function recommendedLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
            ->where('status_id', config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the advance status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

     /**
     * Get verifier of the advance request.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    /**
     * Get the verified log for the advance request.
     */
    public function verifiedLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
            ->where('status_id', config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(AdvanceRequestLog::class, 'advance_request_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function getAdvanceRequestNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2): '';
        return $this->prefix .'-'. $this->advance_number . $fiscalYear;
    }

    public function getApprovedDate()
    {
        return $this->approvedLog ? $this->approvedLog->created_at->toFormattedDateString() : "";
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
    }

    public function getEstimatedAmount()
    {
        return $this->advanceRequestDetails->sum('amount');
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getProjectCode()
    {
        return $this->projectCodes->title;
    }

    public function getRequestDate()
    {
        return $this->request_date->toFormattedDateString();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getRequiredDate()
    {
        return $this->required_date->toFormattedDateString();
    }

    public function getSettlementDate()
    {
        return $this->settlement_date->toFormattedDateString();
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

    public function getVerifierName()
    {
        return $this->verifier->getFullName();
    }

    public function closedLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
            ->whereStatusId(config('constant.CLOSED_STATUS'))
            ->latest();
    }

    public function paidLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
        ->whereStatusId(config('constant.PAID_STATUS'))->latest()->withDefault();
    }

    public function getClosedByName()
    {
        return $this->closedLog ? $this->closedLog->createdBy->getFullName() : '';
    }

    public function getPaidByName()
    {
        return $this->paidLog->createdBy->getFullName();
    }

    public function getClosedDate()
    {
        return $this->closed_at?->toFormattedDateString();
    }
}
