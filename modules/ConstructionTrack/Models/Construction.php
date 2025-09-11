<?php

namespace Modules\ConstructionTrack\Models;

use App\NepaliCalendarApi;
use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;

use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Master\Models\District;
use Modules\Master\Models\DonorCode;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LocalLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ConstructionTrack\Models\ConstructionAmendment;

class Construction extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'constructions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'province_id',
        'district_id',
        'local_level_id',
        'health_facility_name',
        'facility_type',
        'type_of_work',
        'engineer_id',
        'signed_date',
        'effective_date_from',
        'effective_date_to',
        'ohw_contribution',
        'approval',
        'total_contribution_amount',
        'total_contribution_percentage',
        'work_start_date',
        'work_completion_date',
        'donor',
        'metal_plaque_text',
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

    protected $dates = ['signed_date', 'effective_date_from', 'effective_date_to', 'work_start_date', 'work_completion_date'];

    /**
     * Get the approver of a construction
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function constructionParties()
    {
        return $this->hasMany(ConstructionParty::class, 'construction_id');
    }

    public function constructionProgresses()
    {
        return $this->hasMany(ConstructionProgress::class, 'construction_id');
    }

    public function latestConstructionProgress()
    {
        return $this->hasOne(ConstructionProgress::class, 'construction_id')->latestOfMany('report_date')->withDefault();
    }

    public function constructionInstallments()
    {
        return $this->hasMany(ConstructionInstallment::class, 'construction_id');
    }

    public function amendments()
    {
        return $this->hasMany(ConstructionAmendment::class, 'construction_id');
    }

    /**
     * Get the district of the employee.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function donors()
    {
        return $this->belongsToMany(DonorCode::class, 'construction_donors', 'construction_id', 'donor_code_id');
    }

    public function engineer()
    {
        return $this->belongsTo(Employee::class, 'engineer_id')->withDefault();
    }

    public function local()
    {
        return $this->belongsTo(LocalLevel::class, 'local_level_id')->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function latestAmendment()
    {
        return $this->hasOne(ConstructionAmendment::class, 'construction_id')->latestOfMany()->withDefault();
    }

    public function getDistrictName()
    {
        return $this->district->district_name;
    }

    public function getEngineerName()
    {
        return $this->engineer->getFullName();
    }

    /**
     * Get the get office Name.
     */
    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    /**
     * Get the get local Name.
     */
    public function getLocalName()
    {
        return $this->local->getLocalLevelName();
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
     * Get the advance status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(ConstructionLog::class, 'advance_request_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(ConstructionLog::class, 'construction_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getEffectiveFromDate()
    {
        return $this->effective_date_from ? $this->effective_date_from->format('Y-m-d') : '';
    }

    public function getEffectiveFromBsDate()
    {
        $calendar = new NepaliCalendarApi();
        return $this->effective_date_from ? $calendar->englishToNepali($this->effective_date_from->format('Y'), $this->effective_date_from->format('m'), $this->effective_date_from->format('d')) : '';
    }

    public function getEffectiveToDate()
    {
        return $this->effective_date_to ? $this->effective_date_to->format('Y-m-d') : '';
    }

    public function getEffectiveToBsDate()
    {
        $calendar = new NepaliCalendarApi();
        return $this->effective_date_to ? $calendar->englishToNepali($this->effective_date_to->format('Y'), $this->effective_date_to->format('m'), $this->effective_date_to->format('d')) : '';
    }

    public function getSignedBsDate()
    {
        $calendar = new NepaliCalendarApi();
        return $this->signed_date ? $calendar->englishToNepali($this->signed_date->format('Y'), $this->signed_date->format('m'), $this->signed_date->format('d')) : '';
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


    public function getTotalContributionAmount()
    {
        return $this->constructionParties()->sum('contribution_amount');
    }

     public function getTotalContributionPercentage()
    {
        return $this->constructionParties()->sum('contribution_percentage');
    }

    public function getTotalInstallmentAmount()
    {
        return $this->constructionInstallments()->sum('amount');
    }

    public function getTotalFundTransferred()
    {
        $fundTransfers = $this->constructionInstallments()->whereHas('transactionType', function($q) {
            $q->where('title', 'Fund Transferred');
        })->get();

        $totalFundTransferAmount = $fundTransfers->map(function($fundTransfer) {
            return $fundTransfer->amount;
        })->sum();

        return $totalFundTransferAmount;
    }

    public function getTotalExpenseSettled()
    {
        $expenseSettlements = $this->constructionInstallments()->whereHas('transactionType', function($q) {
            $q->where('title', 'Expense Settled');
        })->get();

        $totalExpenseSettlementAmount = $expenseSettlements->map(function($expenseSettlement) {
            return $expenseSettlement->amount;
        })->sum();

        return $totalExpenseSettlementAmount;
    }

    public function getOtherPartiesContribution()
    {
        return $this->constructionParties()
            ->where('deletable', '!=', 0)
            ->sum('contribution_amount');

    }

}
