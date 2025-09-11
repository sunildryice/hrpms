<?php

namespace Modules\FundRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class FundRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fund_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'request_for_office_id',
        'year',
        'month',
        'district_id',
        'project_code_id',
        'prefix',
        'fund_request_number',
        'modification_number',
        'modification_fund_request_id',
        'modification_remarks',
        'remarks',
        'attachment',
        'required_amount',
        'surplus_deficit',
        'estimated_surplus',
        'net_amount',
        'checker_id',
        'certifier_id',
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
     * Get the approved log for the fund request.
     */
    public function approvedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->where('user_id', $this->approver_id)
            ->latest();
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id')->withDefault();
    }

    public function certifier()
    {
        return $this->belongsTo(User::class, 'certifier_id')->withDefault();
    }

    /**
     * Get the reviewer of a fund
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the approver of a fund
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the district of the fund request.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the fund request activities for the fund request.
     */
    public function fundRequestActivities()
    {
        return $this->hasMany(FundRequestActivity::class, 'fund_request_id');
    }

    /**
     * Get the project of the fund request.
     */
    public function projectCode()
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
     * Get requester of the fund request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function checkedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->where('user_id', $this->checker_id)
            ->latest();
    }

    public function certifiedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.VERIFIED2_STATUS'))
            ->where('user_id', $this->certifier_id)
            ->latest();
    }

    /**
     * Get the reviewed log for the fund request.
     */
    public function reviewedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.VERIFIED3_STATUS'))
            ->latest();
    }

    public function recommendedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the fund request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the fund request.
     */
    public function logs()
    {
        return $this->hasMany(FundRequestLog::class, 'fund_request_id');
    }

    public function returnedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->where('status_id', config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get the submitted log for the fund request.
     */
    public function submittedLog()
    {
        return $this->hasOne(FundRequestLog::class, 'fund_request_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    public function getReturnRemarks()
    {
        return $this->returnedLog->log_remarks;
    }

    public function parentFundRequest()
    {
        return $this->belongsTo(FundRequest::class, 'modification_fund_request_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName() ?: $this->office->getDistrictName();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
    }

    public function getMonthName()
    {
        return $this->month ? \DateTime::createFromFormat('!m', $this->month)->format('F') : '';
    }

    public function getFundRequestNumber()
    {
        $number = $this->prefix.'-'.$this->fund_request_number;
        $number .= $this->modification_number ? '-'.$this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->fund_request_number ? $number.$fiscalYear : '';
    }

    public function childFundRequest()
    {
        return $this->hasOne(FundRequest::class, 'modification_fund_request_id');
    }

    public function getOfficeName()
    {
        return $this->office?->getOfficeName();
    }

    public function getProjectCode()
    {
        return $this->projectCode->getProjectCodeWithDescription();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getRequestForOfficeName()
    {
        return $this->requestForOffice?->getOfficeName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getDescription()
    {
        return $this->getOfficeName().' '.$this->getFiscalYear().' '.$this->getMonthName();
    }

    // public function
}
