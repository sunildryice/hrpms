<?php

namespace Modules\Grn\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\InventoryItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

class GrnItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'grn_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grn_id',
        'grnitemable_id',
        'grnitemable_type',
        'item_id',
        'unit_id',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
        'quantity',
        'unit_price',
        'total_price',
        'discount_amount',
        'vat_amount',
        'tds_amount',
        'total_amount',
        'specification',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the grn of the grn item.
     */
    public function grn()
    {
        return $this->belongsTo(Grn::class, 'grn_id')->withDefault();
    }

    /**
     * Get the parent grnitemable model (purchase_request_item or purchase_order_item)
     */
    public function grnitemable()
    {
        return $this->morphTo();
    }

    /**
     * Get the inventoryItem of the grn item.
     */
    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class, 'grn_item_id')->withDefault();
    }

    /**
     * Get the item of the grn item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the purchase order item of the grn item.
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id')->withDefault();
    }

    /**
     * Get the unit of the grn item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    /**
     * Get the accountCode of the grn item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the grn item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the donorCode of the grn item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getSpecification()
    {
        return $this->item->specification;
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDeliveryDate()
    {
        return $this->delivery_date->toFormattedDateString();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getItemName()
    {
        return $this->item->getItemName();
    }

    public function getUnitName()
    {
        return $this->unit->getUnitName();
    }

    public function getPurchaseRequestNumber()
    {
        $purchaseRequest = $this->purchaseOrderItem->purchaseRequestItem->purchaseRequest;
        if ($purchaseRequest) {
            return $purchaseRequest->prefix.'-'.$purchaseRequest->purchase_number;
        }

        return '';
    }

    public function getPurchaseOrderNumber()
    {
        $purchaseOrder = $this->grn->purchaseOrder;
        if ($purchaseOrder) {
            if ($purchaseOrder->prefix && $purchaseOrder->order_number) {
                return $purchaseOrder->prefix.'-'.$purchaseOrder->order_number;
            }
        }

        return '';
    }

    public function getGrnNumber()
    {
        return $this->grn->getGrnNumber();
    }

    public function getPRNo()
    {
        if ($this->grn->grnable_type == "Modules\PurchaseRequest\Models\PurchaseRequest") {
            return $this->grn->getGrnableNumber();
        }
    }

    public function getPONo()
    {
        if ($this->grn->grnable_type == "Modules\PurchaseOrder\Models\PurchaseOrder") {
            return $this->grn->getGrnableNumber();
        }
    }

    public function getOffice()
    {
        return $this->grnitemable?->getOffice();
    }
}
