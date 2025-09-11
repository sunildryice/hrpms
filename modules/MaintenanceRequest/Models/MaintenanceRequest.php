<?php

namespace Modules\MaintenanceRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Item;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class MaintenanceRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'maintenances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'logistic_officer_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'item_id',
        'request_date',
        'prefix',
        'maintenance_number',
        'modification_maintenance_request_id',
        'modification_number',
        'modification_remarks',
        'problem',
        'estimated_cost',
        'remarks',
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
     * Get the approved log for the purchase request.
     */
    public function approvedLog()
    {
        return $this->hasOne(MaintenanceRequestLog::class, 'maintenance_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the Donor Code
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the createdBy
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the logisticOfficer
     */
    public function logisticOfficer()
    {
        return $this->belongsTo(User::class, 'logistic_officer_id')->withDefault();
    }

    /**
     * Get the logs for the maintenance request.
     */
    public function logs()
    {
        return $this->hasMany(MaintenanceRequestLog::class, 'maintenance_id');
    }

    /**
     * Get the maintenance Request Items for the maintenance request.
     */
    public function maintenanceRequestItems()
    {
        return $this->hasMany(MaintenanceRequestItem::class, 'maintenance_id');
    }

    public function parentMaintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'modification_maintenance_request_id');
    }

    /**
     * Get the office
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the requester
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the reviewed log for the purchase request.
     */
    public function reviewedLog()
    {
        return $this->hasOne(MaintenanceRequestLog::class, 'maintenance_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    /**
     * Get the reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the local travel.
     */
    public function submittedLog()
    {
        return $this->hasOne(MaintenanceRequestLog::class, 'maintenance_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    public function returnLog()
    {
        return $this->hasOne(MaintenanceRequestLog::class, 'maintenance_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest();
    }


    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->full_name;
    }

    public function getItem()
    {
        return $this->item->title;
    }

    public function getMaintenanceRequestNumber()
    {
        $maintenanceNumebr = $this->prefix.'-'.$this->maintenance_number;
        $maintenanceNumebr .= $this->modification_number ? '-'.$this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->maintenance_number ? $maintenanceNumebr.$fiscalYear : '';
    }

    public function getLogisticOfficerName()
    {
        return $this->logisticOfficer->full_name;
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getRequesterName()
    {
        return $this->requester->full_name;
    }

    public function getReviewer()
    {
        return $this->reviewer->full_name;
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }
}
