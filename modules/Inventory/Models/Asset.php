<?php

namespace Modules\Inventory\Models;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\Office;
use Modules\Privilege\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\AssetDisposition\Models\AssetDisposition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetDisposition\Models\DispositionRequest;
use Modules\AssetDisposition\Models\DispositionRequestAsset;

class Asset extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_item_id',
        'prefix',
        'year',
        'fiscal_year',
        'asset_number',
        'old_asset_code',
        'assigned_office_id',
        'assigned_department_id',
        'assigned_user_id',
        'status',
        'serial_number',
        'voucher_number',
        'room_number',
        'condition_id',
        'remarks'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = ['asset_code'];

    /**
     * Get the inventory of the asset.
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get all assign logs for the asset.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assetAssignLogs()
    {
        return $this->hasMany(AssetAssignLog::class, 'asset_id');
    }

    public function assetConditionLogs()
    {
        return $this->hasMany(AssetConditionLog::class, 'asset_id');
    }

    public function assignedOffice()
    {
        return $this->belongsTo(Office::class, 'assigned_office_id');
    }

      /**
     * get latest assign log for the asset.
     * @return mixed
     */
    public function latestConditionLog()
    {
        return $this->hasOne(AssetConditionLog::class, 'asset_id')->latest();
    }

    /**
     * Get all logs for the asset.
     */
    public function logs()
    {
        return $this->hasMany(AssetLog::class, 'asset_id');
    }

    public function getAssetNumber()
    {
        return $this->prefix .'/'. sprintf('%03d',$this->asset_number).'/'.$this->year;
    }

    public function getAssetCodeAttribute()
    {
        return $this->prefix .'/'. sprintf('%03d',$this->asset_number).'/'.$this->year;
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withDefault();
    }

    public function goodRequestAsset()
    {
        return $this->hasMany(GoodRequestAsset::class, 'assign_asset_id');
    }

    public function disposition()
    {
        return $this->hasOne(DispositionRequestAsset::class, 'asset_id');
    }

    public function dispositionRequest()
    {
        return $this->hasOneThrough(DispositionRequest::class, DispositionRequestAsset::class, 'asset_id', 'id', 'id', 'disposition_request_id');
    }

    public function latestGoodRequestAsset()
    {
        return $this->hasOne(GoodRequestAsset::class, 'assign_asset_id')->withDefault([
            'assigned_user_id' => 0
        ])->latest();
    }

    public function getAssignedUserName()
    {
        return $this->assignedTo->getFullName();
    }

    public function getAssignedUserDesignation()
    {
        return $this->assignedTo->employee->latestTenure->getDesignationName();
    }

    public function getAssignedUserOfficeLocation()
    {
        return $this->assignedTo->employee->latestTenure->getOfficeName();
    }

    public function getAssignedOffice()
    {
        return $this->assignedOffice?->office_name;
    }

    public function getAssignedUserOfficeDistrict()
    {
        return $this->assignedTo->employee->latestTenure->getDutyStation();
    }

    public function getAssignedUserOfficeCode()
    {
        return $this->assignedTo->employee->latestTenure->office->getOfficeCode();
    }

    public function getAssetCondition()
    {
        return $this->latestConditionLog?->condition?->title;
    }

    public function getItemCode()
    {
        return $this->inventoryItem->item->item_code;
    }

    public function getItemName()
    {
        return $this->inventoryItem->getItemName();
    }

    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    public function getSpecification()
    {
        return $this->inventoryItem->specification;
    }

    public function getPrice()
    {
        return $this->inventoryItem->getTotalPrice();
    }

    public function getPurchaseDate()
    {
        return $this->inventoryItem->getPurchaseDate();
    }

    public function getIssuedDate()
    {
        if ($this->status == config('constant.ASSET_ASSIGNED')) {
            return $this->latestGoodRequestAsset?->goodRequest?->approvedLog?->created_at?->toFormattedDateString();
        } else {
            return '';
        }
    }

    public function getDispositionType()
    {
        return $this->dispositionRequest?->dispositionType?->title;
    }

    public function getDispositionDate()
    {
        return $this->dispositionRequest?->disposition_date?->toFormattedDateString();
    }

    public function getDisposedBy()
    {
        return $this->dispositionRequest?->requester?->getFullName();
    }

    public function getDispositionOffice()
    {
        return $this->dispositionRequest?->getOfficeName();
    }

    public function isDisposed()
    {
        return $this->dispositionRequest?->status_id == config('constant.APPROVED_STATUS');
    }

}
