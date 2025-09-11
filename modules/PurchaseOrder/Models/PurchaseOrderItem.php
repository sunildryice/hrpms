<?php

namespace Modules\PurchaseOrder\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Grn\Models\GrnItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;
use Modules\PaymentSheet\Models\PaymentSheetDetail;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class PurchaseOrderItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_id',
        'purchase_request_item_id',
        'item_id',
        'unit_id',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
        'specification',
        'remarks',
        'delivery_date',
        'quantity',
        'unit_price',
        'total_price',
        'vat_amount',
        'total_amount',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['delivery_date'];

    /**
     * Get the purchase order of the purchase order item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the item of the purchase order item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the purchase request item of the purchase order item.
     */
    public function purchaseRequestItem()
    {
        return $this->belongsTo(PurchaseRequestItem::class, 'purchase_request_item_id')->withDefault();
    }

    /**
     * Get the unit of the purchase order item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    /**
     * Get the accountCode of the purchase order item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the purchase order item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the donorCode of the purchase order item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    // /**
    //  * Get the grn items of the purchase order item.
    //  */
    // public function grnItems()
    // {
    //     return $this->hasMany(GrnItem::class, 'purchase_order_item_id');
    // }

    /**
     * Get all the purchase order item's grn items.
     */
    public function grnItems()
    {
        return $this->morphMany(GrnItem::class, 'grnitemable');
    }

    public function paymentSheetDetails()
    {
        return $this->belongsToMany(PaymentSheetDetail::class, 'po_item_ps_detail', 'po_item_id', 'ps_detail_id');
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDeliveryDate()
    {
        return $this->delivery_date ? $this->delivery_date->toFormattedDateString() : '';
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

    public function getOffice()
    {
        return $this->purchaseRequestItem->getOffice();
    }
}
