<?php

namespace Modules\Inventory\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Modules\Master\Models\Condition;
use Modules\Master\Models\Department;
use Modules\Master\Models\Office;
use Modules\Privilege\Models\User;

class AssetAssignLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'asset_assign_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asset_id',
        'good_request_asset_id',
        'assigned_office_id',
        'assigned_department_id',
        'assigned_user_id',
        'condition_id',
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
     * Get the asset of the assign log.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the assignedDepartment of the assign log.
     */
    public function assignedDepartment()
    {
        return $this->belongsTo(Department::class, 'assigned_department_id')->withDefault();
    }

    /**
     * Get the assignedOffice of the assign log.
     */
    public function assignedOffice()
    {
        return $this->belongsTo(Office::class, 'assigned_office_id')->withDefault();
    }

    /**
     * Get the assignedUser of the assign log.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withDefault();
    }

    /**
     * Get the createdBy of the assign log.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the condition of the asset assign log.
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id')->withDefault();
    }

    /**
     * Get the goodRequestAsset of the assign log.
     */
    public function goodRequestAsset()
    {
        return $this->belongsTo(GoodRequestAsset::class, 'good_request_asset_id')->withDefault();
    }

    public function getAssetNumber()
    {
        return $this->asset ?->getAssetNumber();
    }

    public function getAssignedDepartment()
    {
        return $this->assignedDepartment->getDepartmentName();
    }

    public function getAssignedDistrict()
    {
        return '';
    }

    public function getAssignedOffice()
    {
        return $this->assignedOffice->getOfficeName();
    }

    public function getAssignedUser()
    {
        return $this->assignedUser->getFullName();
    }

    public function getCreatedAt()
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    public function getCreatedDate()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    } 

    public function getDesignation()
    {
        return $this->createdBy->employee->tenures()->where('created_at', '<=', $this->created_at)->latest()->first()->getDesignationName() ?? '';
    }


    public function getCondition()
    {
        return $this->condition->getTitle();
    }
}
