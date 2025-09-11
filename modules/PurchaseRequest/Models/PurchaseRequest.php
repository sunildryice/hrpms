<?php

namespace Modules\PurchaseRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Attachment\Models\Attachment;
use Modules\Grn\Models\Grn;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Package;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

class PurchaseRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'requester_id',
        'reviewer_id',
        'verifier_id',
        'budget_verifier_id',
        'recommender_id',
        'approver_id',
        'fiscal_year_id',
        'office_id',
        'prefix',
        'purchase_number',
        'modification_number',
        'modification_purchase_request_id',
        'modification_remarks',
        'verify_remarks',
        'required_date',
        'request_date',
        'purpose',
        'delivery_instructions',
        'budgeted',
        'budget_description',
        'total_amount',
        'balance_budget',
        'commitment_amount',
        'estimated_balance_budget',
        'status_id',
        'close_remarks',
        'open_remarks',
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

    protected $dates = ['required_date', 'request_date'];

    /**
     * Get the approved log for the purchase request.
     */
    public function approvedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver of a purchase
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get all the Purchase Request's attachments
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the district of the purchase request.
     */
    public function districts()
    {
        return $this->belongsToMany(District::class, 'purchase_request_districts', 'purchase_request_id', 'district_id');
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get all the purchase_request's grns.
     */
    public function grns()
    {
        return $this->morphMany(Grn::class, 'grnable');
    }


    public function budgetVerifier()
    {
        return $this->belongsTo(User::class, 'budget_verifier_id')->withDefault();
    }

    /**
     * Get the logs for the purchase request.
     */
    public function logs()
    {
        return $this->hasMany(PurchaseRequestLog::class, 'purchase_request_id')
            ->orderBy('created_at', 'desc');
    }

    public function latestLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')->latestOfMany();
    }

    public function closedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.CLOSED_STATUS'))
            ->latest();
    }

    public function getClosedByName()
    {
        return $this->closedLog ? $this->closedLog->createdBy->getFullName() : '';
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function purchaseRequestBudgets()
    {
        return $this->hasMany(PurchaseRequestBudget::class, 'purchase_request_id');
    }

    /**
     * Get the purchase items for the purchase request.
     */
    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'purchase_request_id');
    }

    /**
     * Get parent of the modified purchase request.
     */
    public function parentPurchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'modification_purchase_request_id');
    }

    /**
     * Get modified child request of a purchase request
     */
    public function childPurchaseRequest()
    {
        return $this->hasOne(PurchaseRequest::class, 'modification_purchase_request_id');
    }

    /**
     * Get requester of the purchase request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get second reviewer of the purchase request
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    /**
     * Get the reviewed log for the purchase request.
     */
    public function reviewedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    public function budgetVerifiedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get reviewer of the purchase request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    /**
     * Get the purchase status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function procurementOfficers()
    {
        return $this->belongsToMany(User::class, 'purchase_request_procurements', 'purchase_request_id', 'officer_id');
    }

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_request_order', 'pr_id', 'po_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasManyThrough(PurchaseOrderItem::class, PurchaseRequestItem::class, 'purchase_request_id', 'purchase_request_item_id', 'id', 'id')
            ->whereDoesntHave('purchaseOrder', function ($q) {
                $q->where('status_id', config('constant.CANCELLED_STATUS'));
            });
    }

    public function packages()
    {
        return $this->hasManyThrough(Package::class, PurchaseRequestItem::class, 'purchase_request_id', 'id', 'id', 'package_id');
    }

    public function getPurchaseOrderItems()
    {
        return $this->purchaseOrderItems()->get();
    }

    public function recommendReviewLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.RECOMMENDED2_STATUS'))
            ->latest();
    }

    /**
     * Get the submitted log for the purchase request.
     */
    public function submittedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    public function recommendedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->where(function ($q) {
                $q->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')]);
                $q->where('user_id', $this->recommender_id);
            })
            ->latest();
    }

    public function getRecommendedDate()
    {
        $log = $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->where(function ($q) {
                $q->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')]);
                $q->where('user_id', $this->recommender_id);
            })->latest()->first();
        return isset($log) ? $log->created_at : '';
    }

    /**
     * Get the verified log for the purchase request.
     */
    public function verifiedLog()
    {
        return $this->hasOne(PurchaseRequestLog::class, 'purchase_request_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getBudgeted()
    {
        return $this->budgeted == 1 ? 'Yes' : 'No';
    }

    public function getBudgetDescription()
    {
        return $this->budgeted != 1 ? $this->budget_description : '';
    }

    public function getDistrictNames()
    {
        return $this->districts ? implode(', ', $this->districts->pluck('district_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    public function getEstimatedAmount()
    {
        return number_format($this->purchaseRequestItems->sum('total_price'), 2);
    }

    public function getGrnableNumber()
    {
        return $this->getPurchaseRequestNumber();
    }

    public function getGrnableDate()
    {
        return $this->getRequestDate();
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getPurchaseRequestNumber()
    {
        $purchaseRequestNumber = $this->prefix . '-' . $this->purchase_number;
        $purchaseRequestNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';
        return $this->purchase_number ? $purchaseRequestNumber . $fiscalYear : '';
    }

    public function getRequestDate()
    {
        return $this->request_date ? $this->request_date->toFormattedDateString() : '';
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    /**
     * Get name of verifier(second reviewer)
     * @return mixed
     */
    public function getVerifierName()
    {
        return $this->verifier->getFullName();
    }

    public function getRequiredDate()
    {
        return $this->required_date->toFormattedDateString();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function verified()
    {
        if ($this->parentPurchaseRequest) {
            return true;
        }
        return $this->logs->where('status_id', config('constant.VERIFIED_STATUS'))->count() > 0;
    }

    public function verificationRequired()
    {
        // return (($this->total_amount >= config('constant.PR_REVIEW_THRESHOLD')) && !$this->verified());
        return ($this->total_amount >= config('constant.PR_REVIEW_THRESHOLD'));
    }

    public function getBudgetVerifier()
    {
        return $this->budgetVerifier->getFullName();
    }

    public function getRecommender()
    {
        return $this->recommender->getFullName();
    }

    public function getReviewLog()
    {
        return $this->recommendReviewLog ?? $this->reviewLog;
    }
}
