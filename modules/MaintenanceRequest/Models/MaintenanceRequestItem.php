<?php

namespace Modules\MaintenanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Privilege\Models\User;

class MaintenanceRequestItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'maintenance_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maintenance_id',
        // 'activity_code_id',
        // 'account_code_id',
        // 'donor_code_id',
        'item_id',
        'asset_id',
        'problem',
        'replacement_good_needed',
        // 'estimated_cost',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the Account Code
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the Activity Code
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the createdBy
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the Donor Code
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the item of the maintenance item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the Maintenance Request of the item.
     */
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_id');
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCode();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCode();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCode();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getItemName()
    {
        return $this->item ? $this->item->getItemName() : '';
    }
}
