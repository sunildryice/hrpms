<?php

namespace Modules\PurchaseOrder\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Grn\Models\Grn;
use Modules\Lta\Models\LtaContract;
use Modules\Master\Models\Currency;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\Privilege\Models\User;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use Modules\Supplier\Models\Supplier;

class PurchaseOrder extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reviewer_id',
        'approver_id',
        'fiscal_year_id',
        'office_id',
        'supplier_id',
        'lta_contract_id',
        'prefix',
        'order_number',
        'order_date',
        'delivery_date',
        'delivery_location',
        'delivery_instructions',
        'currency_id',
        'purpose',
        'sub_total',
        'vat_amount',
        'other_charge_amount',
        'total_amount',
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

    protected $dates = ['required_date', 'order_date', 'delivery_date'];

    /**
     * Get the approver of a purchase order
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the createdBy of a purchase
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    /**
     * Get the district of the purchase order.
     */
    public function districts()
    {
        return $this->belongsToMany(District::class, 'purchase_order_districts', 'purchase_order_id', 'district_id');
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get all the purchase_order's grns.
     */
    public function grns()
    {
        return $this->morphMany(Grn::class, 'grnable');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function purchaseRequests()
    {
        return $this->belongsToMany(PurchaseRequest::class, 'purchase_request_order', 'po_id', 'pr_id');
    }

    public function paymentSheets()
    {
        return $this->belongsToMany(PaymentSheet::class, 'payment_sheet_purchase_orders', 'purchase_order_id', 'payment_sheet_id');
    }

    /**
     * Get the purchase items for the purchase order.
     */
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function grn()
    {
        return $this->hasMany(Grn::class, 'purchase_order_id');
    }

    /**
     * Get the reviewer of a purchase order
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the purchase status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get supplier of the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    public function ltaContract()
    {
        return $this->belongsTo(LtaContract::class, 'lta_contract_id')->withDefault();
    }

    /**
     * Get the logs for the purchase order.
     */
    public function logs()
    {
        return $this->hasMany(PurchaseOrderLog::class, 'purchase_order_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the submitted log for the purchase order.
     */
    public function submittedLog()
    {
        return $this->hasOne(PurchaseOrderLog::class, 'purchase_order_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the approved log for the purchase order.
     */
    public function approvedLog()
    {
        return $this->hasOne(PurchaseOrderLog::class, 'purchase_order_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approved log for the purchase order.
     */
    public function reviewedLog()
    {
        return $this->hasOne(PurchaseOrderLog::class, 'purchase_order_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getCurrency()
    {
        return $this->currency->getTitle();
    }

    public function getDeliveryDate()
    {
        return $this->delivery_date?->toFormattedDateString();
    }

    public function getDistrictNames()
    {
        return $this->districts ? implode(', ', $this->districts->pluck('district_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    public function getGrnableNumber()
    {
        return $this->getPurchaseOrderNumber();
    }

    public function getGrnableDate()
    {
        return $this->getOrderDate();
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getOrderDate()
    {
        return $this->order_date ? $this->order_date->toFormattedDateString() : '';
    }

    public function getPurchaseOrderNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->prefix.'-'.$this->order_number.$fiscalYear;
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

    public function getSupplierName()
    {
        return $this->supplier->supplier_name;
    }

    public function getSupplierNameTotalandDate()
    {
        return $this->supplier->supplier_name.' - '.$this->sub_total.' - '.$this->delivery_date->toFormattedDateString();
    }

    public function getContractNumber()
    {
        return $this->ltaContract?->contract_number;
    }

    public function getSummary()
    {
        $items = $this->purchaseOrderItems()
            ->with(['activityCode', 'donorCode', 'accountCode', 'purchaseRequestItem', 'purchaseRequestItem.office' ])
            ->withAggregate('purchaseRequestItem', 'office_id')
            ->get();

        return $items
            ->groupBy(['purchase_request_item_office_id', 'activity_code_id', 'donor_code_id', 'account_code_id'])
            ->flatten(3)
            ->map(function ($poItems) {
                $poItem = $poItems->first();
                $poItem->po_item_ids = $poItems->implode('id', ', ');
                $poItem->total_amount = $poItems->sum('total_amount');
                $poItem->vat_amount = $poItems->sum('vat_amount');
                $poItem->total_price = $poItems->sum('total_price');
                $poItem->specification = $poItems->implode('specification', ', ');
                return $poItem;
            });
    }
}
