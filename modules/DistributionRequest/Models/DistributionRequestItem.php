<?php

namespace Modules\DistributionRequest\Models;

use App\Traits\ModelEventLogger;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\InventoryItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;
use Modules\Privilege\Models\User;

class DistributionRequestItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'distribution_request_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'distribution_request_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'inventory_item_id',
        'item_id',
        'unit_id',
        'specification',
        'quantity',
        'unit_price',
        'total_amount',
        'vat_amount',
        'net_amount',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the activityCode of the distribution request item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the accountCode of the distribution request item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the distribution request of the distribution request item.
     */
    public function distributionRequest()
    {
        return $this->belongsTo(DistributionRequest::class, 'distribution_request_id');
    }

    /**
     * Get the donorCode of the distribution request item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the inventory item of the distribution request item.
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id')->withDefault();
    }

    /**
     * Get the item of the distribution request item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the unit of the distribution request item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
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

    public function getExpiryDate()
    {
        return $this->inventoryItem ? $this->inventoryItem->getExpiryDate() : '';
    }

    public function getInventoryItem()
    {
        return $this->inventoryItem ? $this->inventoryItem->getItemName() : '';
    }

    public function getItemName()
    {
        return $this->item ? $this->item->getItemName() : '';
    }

    public function getUnit()
    {
        return $this->unit->getUnitName();
    }
}
