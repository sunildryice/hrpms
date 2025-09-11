<?php

namespace Modules\Grn\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Supplier\Models\Supplier;

class Grn extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'grns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'grnable_id',
        'grnable_type',
        'office_id',
        'supplier_id',
        'prefix',
        'grn_number',
        'invoice_number',
        'received_date',
        'sub_total',
        'other_charge_amount',
        'discount_amount',
        'vat_amount',
        'total_amount',
        'tds_amount',
        'grn_amount',
        'received_note',
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

    protected $dates = ['received_date'];

    /**
     * Get the approved log for the grn.
     */
    public function approvedLog()
    {
        return $this->hasOne(GrnLog::class, 'grn_id')
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
     * Get the createdBy of a purchase
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the district of the grn.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    // /**
    //  * Get the purchase order of the grn.
    //  */
    // public function purchaseOrder()
    // {
    //     return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id')->withDefault();
    // }

    /**
     * Get the parent grnable model (purchase_request or purchase_order).
     */
    public function grnable()
    {
        return $this->morphTo();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get supplier of the grn.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    /**
     * Get the purchase status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the grn.
     */
    public function logs()
    {
        return $this->hasMany(GrnLog::class, 'grn_id');
    }

    /**
     * Get the grn items for the grn.
     */
    public function grnItems()
    {
        return $this->hasMany(GrnItem::class, 'grn_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getDonorCodes()
    {
        return $this->grnItems->map(function($item){
            return $item->getDonorCode();
        })->unique()->implode(',');
    }

    public function getActivityCodes()
    {
        return $this->grnItems->map(function($item){
            return $item->activityCode->title;
        })->unique()->implode(', ');
    }

    public function getAccountCodes()
    {
        return $this->grnItems->map(function($item){
            return $item->accountCode->title;
        })->unique()->implode(', ');
    }

    public function getEstimatedAmount()
    {
        return $this->purchaseOrderItems->sum('total_price');
    }

    public function getGrnNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2): '';
        return $this->prefix .'-'. $this->grn_number . $fiscalYear;
    }

    public function getGrnableNumber()
    {
        return $this->grnable ? $this->grnable->getGrnableNumber() : '';
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->getOfficeName() : '';
    }

    public function getReceivedDate()
    {
        return $this->received_date ? $this->received_date->toFormattedDateString() : '';
    }

    public function getStatus()
    {
        $status = $this->status->title == 'approved' ? 'received' : $this->status->title;
        return ucwords($status);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getSupplierName()
    {
        return $this->supplier->supplier_name;
    }

    public function hasAssets(): bool
    {
        $flag = false;
        foreach ($this->grnItems as $item) {
            if ($item->inventoryItem->assets()->count() > 0) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }
}
