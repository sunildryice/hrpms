<?php

namespace Modules\AdvanceRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AdvanceRequest\Models\SettlementRequestLog;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class Settlement extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_settlements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_request_id',
        'office_id',
        'fiscal_year_id',
        'prefix',
        'settlement_number',
        'project_code_id',
        'reviewer_id',
        'verifier_id',
        'recommender_id',
        'approver_id',
        'completion_date',
        'advance_amount',
        'status_id',
        'reason_for_over_or_under_spending',
        'remarks',
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

    protected $dates = ['completion_date', 'pay_date'];

    /**
     * Get the advance order of the advance order item.
     */
    public function advanceRequest()
    {
        return $this->belongsTo(AdvanceRequest::class, 'advance_request_id');
    }

    /**
     * Get the approved log for the advance settlement.
     */
    public function approvedLog()
    {
        return $this->hasOne(SettlementRequestLog::class, 'advance_settlement_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver of the advance settlement
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the verifier of the advance settlement
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(SettlementRequestLog::class, 'advance_settlement_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the project code.
     */
    public function projectCode()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    /**
     * Get the recommender of the advance settlement
     */
    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    /**
     * Get the recommended log for the advance settlement.
     */
    public function recommendedLog()
    {
        return $this->hasOne(SettlementRequestLog::class, 'advance_settlement_id')
            ->where('status_id', config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the requester of the advance settlement
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the reviewer of the advance settlement
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the reviewed log for the advance settlement.
     */
    public function reviewedLog()
    {
        return $this->hasOne(SettlementRequestLog::class, 'advance_settlement_id')
            ->where('status_id', config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    /**
     * Get the activities of the settlement.
     */
    public function settlementActivities()
    {
        return $this->hasMany(SettlementActivity::class, 'advance_settlement_id');
    }

    /**
     * Get the expenses of the settlement.
     */
    public function settlementExpenses()
    {
        return $this->hasMany(SettlementExpense::class, 'advance_settlement_id');
    }

    /**
     * Get the expense details of the settlement.
     */
    public function settlementExpenseDetails()
    {
        return $this->hasMany(SettlementExpenseDetail::class, 'advance_settlement_id');
    }

    /**
     * Get the advance status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApprovedDate()
    {
        return $this->approvedLog ? $this->approvedLog->created_at->toFormattedDateString() : "";
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCompletionDate()
    {
        return $this->completion_date->toFormattedDateString();
    }
    public function getPaymentDate()
    {
        return $this->pay_date->toFormattedDateString();
    }

    public function getCashSurplusDeficit()
    {
        return $this->advance_amount - $this->expenditurePaid();
    }

    public function expenditurePaid()
    {
        return $this->settlementExpenses->sum('net_amount');
    }

    public function getProjectCode()
    {
        return $this->projectCode->getProjectCodeWithDescription();
    }

    public function getRecommenderName()
    {
        return $this->recommender->getFullName();
    }

    public function getRequesterName()
    {
        return $this->advanceRequest->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getSettlementNumber()
    {
        return $this->prefix . '-' . $this->settlement_number;
    }

    public function getSettlementExpenseAmount()
    {
        return $this->settlementExpenses->sum('gross_amount');
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getTotaldays($submitted_date)
    {
        return $this->submitted_date ? $this->submitted_date->diffInDays($this->completion_date) : 1;
    }

    public function getTotalExpenses()
    {
        return $this->settlementExpenses->sum('gross_amount');
    }

    public function getTotalTDS()
    {
        return $this->settlementExpenses->sum('tax_amount');
    }

    public function getTotalTurnAroundDays()
    {
        return ($this->approvedLog && $this->advanceRequest->approvedLog) ?
        $this->approvedLog->created_at->diffInDays($this->advanceRequest->approvedLog->created_at) : '';
    }

    public function paidLog()
    {
        return $this->hasOne(SettlementRequestLog::class, 'advance_settlement_id')
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->latest();
    }

    public function getPayerName()
    {
        return $this->paidLog()?->first()->createdBy->getFullName();
    }

    public function getPayerDesignation()
    {
        return $this->paidLog()?->first()->createdBy->employee->designation->title;
    }
}
