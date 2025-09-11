<?php

namespace Modules\PurchaseRequest\Models;

use App\Traits\ModelEventLogger;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Grn\Models\GrnItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\District;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Office;
use Modules\Master\Models\Package;
use Modules\Master\Models\Unit;
use Modules\Privilege\Models\User;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

class PurchaseRequestItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_request_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_request_id',
        'item_id',
        'package_id',
        'unit_id',
        'district_id',
        'office_id',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
        'specification',
        'remarks',
        'quantity',
        'unit_price',
        'total_price',
        'created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the purchase request of the purchase request item.
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    /**
     * Get all the purchase request item's grn items.
     */
    public function grnItems()
    {
        return $this->morphMany(GrnItem::class, 'grnitemable');
    }

    /**
     * Get the item of the purchase request item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id')->withDefault();
    }

    /**
     * Get the unit of the purchase request item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    /**
     * Get the accountCode of the purchase request item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the purchase request item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the donorCode of the purchase request item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the purchase order item of the purchase request item.
     */
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_request_item_id')
            ->whereDoesntHave('purchaseOrder', function ($q){
                $q->where('status_id', config('constant.CANCELLED_STATUS'));
            });
    }
    // /**
    //  * Get the purchase order item of the purchase request item.
    //  */
    // public function purchaseOrderItem()
    // {
    //     return $this->hasOne(PurchaseOrderItem::class, 'purchase_request_item_id')->withDefault();
    // }

    public function getDistrict()
    {
        return $this->district->getDistrictName();
    }

    public function getOffice()
    {
        return $this->office->getOfficeName();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
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

    public function getPackageName()
    {
        return $this->package?->package_name;
    }

    public function getItemNameWithPackage()
    {
        if($this->package_id){
            return  $this->package?->package_name . ': ' . $this->item->getItemName();
        }
        return $this->item->getItemName();
    }

    public function getPOItemName()
    {
        return $this->purchaseOrderItems()->first()?->getItemName() . ' '.$this->purchaseOrderItems()->first()?->purchaseOrder->getPurchaseOrderNumber() ;
    }

    public function getGrnItems()
    {
        // $grnItems = $this->grnItems()->with('inventoryItem')->get();
        // $poGrnItems = $this->purchaseOrderItem()->first()?->grnItems()->with('inventoryItem')->get();
        // if($poGrnItems != null){
        //     $grnItems = $grnItems?->merge($poGrnItems);
        // }
        // return $grnItems;
        $this->load('grnItems.inventoryItem');
        // $this->load('grnItems.inventoryItem', 'purchaseOrderItem.grnItems.inventoryItem');
        $grnItems = $this->grnItems;
        if($this->purchaseOrderItems()->first()?->grnItems){
            $grnItems = $this->grnItems->merge($this->purchaseOrderItems()->first()?->grnItems);
        }
        return $grnItems;
    }

    public function getGrnItemsCsv()
    {
        $grnItems = $this->getGrnItems();
            $grnItems = $grnItems->map(function ($item) {
                return $item->getItemName().' '. $item->grn->getGrnNumber();
            })->toArray();
            return implode('<br> ', $grnItems);
    }

    public function getInventoryItems()
    {
        $grnItems = $this->getGrnItems();
        return $grnItems->filter(function ($grnItem) {
            $inventoryItem = $grnItem->inventoryItem;
            return $inventoryItem;// && $inventoryItem->quantity > $inventoryItem->assigned_quantity && $inventoryItem->distribution_type_id == 2;
        })->pluck('inventoryItem');
    }

    public function getInventoryItemCsv()
    {
        $inventoryItems = $this->getInventoryItems();
            $inventoryItems = $inventoryItems->map(function ($item) {
                return $item->getItemName() . ' Batch:' . $item->batch_number;
            })->toArray();
            return  implode('<br>', $inventoryItems);
    }
}
