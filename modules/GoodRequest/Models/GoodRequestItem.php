<?php

namespace Modules\GoodRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\InventoryItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;

class GoodRequestItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'good_request_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'good_request_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'item_name',
        'unit_id',
        'quantity',
        'specification',
        'inventory_category_id',
        'assigned_inventory_item_id',
        'assigned_item_id',
        'assigned_unit_id',
        'assigned_quantity',
        'assigned_asset_ids',
        'assigned_specification',
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
     * Get the assigned item of a good request
     */
    public function assignedItem()
    {
        return $this->belongsTo(Item::class, 'assigned_item_id')->withDefault();
    }

    public function assignedInventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'assigned_inventory_item_id')->withDefault();
    }

    public function getInventoryItemName()
    {
        return $this->assignedInventoryItem?->getItemName();
    }

    /**
     * Get the assigned unit of a good request
     */
    public function assignedUnit()
    {
        return $this->belongsTo(Unit::class, 'assigned_unit_id')->withDefault();
    }

    /**
     * Get the donorCode of the distribution request item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the good request that owns good request item
     */
    public function goodRequest()
    {
        return $this->belongsTo(GoodRequest::class, 'good_request_id');
    }

    /**
     * Get the good request assets of a good request item
     */
    public function goodRequestAssets()
    {
        return $this->hasMany(GoodRequestAsset::class, 'good_request_item_id');
    }

    /**
     * Get the unit of good request.
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

    public function getUnit()
    {
        return $this->unit->title;
    }
}
