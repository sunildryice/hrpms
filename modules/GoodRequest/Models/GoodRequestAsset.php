<?php

namespace Modules\GoodRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Asset;
use Modules\Master\Models\AssetStatus;
use Modules\Master\Models\Condition;
use Modules\Master\Models\Department;
use Modules\Master\Models\District;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class GoodRequestAsset extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'good_request_assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'good_request_id',
        'good_request_item_id',
        'assign_asset_id',
        'asset_condition_id',
        'asset_condition',
        'room_number',
        'status',

        'assigned_user_id',
        'assigned_district_id',
        'assigned_office_id',
        'assigned_department_id',
        'assigned_on',

        'reviewer_id',
        'approver_id',
        'handover_status_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['assigned_on'];

    /**
     * Get the approver of a good request asset
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the asset of the good request assigned asset.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'assign_asset_id')->withDefault();
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withDefault();
    }

    public function assignedDistrict()
    {
        return $this->belongsTo(District::class, 'assigned_district_id')->withDefault();
    }

    public function assignedOffice()
    {
        return $this->belongsTo(Office::class, 'assigned_office_id')->withDefault();
    }

    /**
     * Get the assigned department of the asset assign log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedDepartment()
    {
        return $this->belongsTo(Department::class, 'assigned_department_id')->withDefault();
    }

    /**
     * Get the condition of the asset assign log.
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class, 'asset_condition_id')->withDefault();
    }

    /**
     * Get the good request of the assigned asset.
     */
    public function goodRequest()
    {
        return $this->belongsTo(GoodRequest::class, 'good_request_id');
    }

    /**
     * Get the good request item of the assigned asset.
     */
    public function goodRequestItem()
    {
        return $this->belongsTo(GoodRequestItem::class, 'good_request_item_id');
    }

    /**
     * Get the logs for the good request asset.
     */
    public function logs()
    {
        return $this->hasMany(GoodRequestAssetLog::class, 'good_request_asset_id');
    }

    /**
     * Get the latest submitted log
     * @return mixed
     */
    public function submittedLog()
    {
        return $this->hasOne(GoodRequestAssetLog::class, 'good_request_asset_id')
            ->where('handover_status_id',config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * get submitted remarks
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->submittedLog?->log_remarks;
    }

    /**
     * Get the reviewer of a good request asset
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the good request asset handover status.
     */
    public function handoverStatus()
    {
        return $this->belongsTo(Status::class, 'handover_status_id')->withDefault();
    }

    public function assetStatus()
    {
        return $this->belongsTo(AssetStatus::class, 'status')->withDefault();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withDefault();
    }

    public function getCreatedDate()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function getCondition()
    {
        return $this->condition ?->getTitle();
    }

    public function getStatus()
    {
            return $this->handoverStatus->title;
    }

    public function getStatusClass()
    {
            return $this->handoverStatus->status_class;
    }

    public function getAssetStatus()
    {
        return $this->assetStatus->title;
    }

    public function getAssetStatusClass()
    {
        return $this->assetStatus->status_class;
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getAssetNumber()
    {
        return $this->asset->getAssetNumber();
    }

    public function getAssignedUser()
    {
        return $this->assignedTo->getFullName();
    }

    public function getAssignedOffice()
    {
        return $this->assignedOffice->getOfficeName();
    }

    public function getAssignedDepartment()
    {
        return $this->assignedDepartment->getDepartmentName();
    }

    public function getAssignedDistrict()
    {
        return $this->assignedDistrict->getDistrictName();
    }

    public function getAssignedDate()
    {
        return $this->assigned_on?->toFormattedDateString();
    }

    public function getSpecification()
    {
        return $this->asset->inventoryItem->specification;
    }

    public function getAssetCondition()
    {
        return $this->asset->getAssetCondition();
    }
}
