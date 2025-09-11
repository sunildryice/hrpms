<?php

namespace Modules\DistributionRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Attachment\Models\Attachment;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\HealthFacility;
use Modules\Master\Models\LocalLevel;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class DistributionHandover extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'distribution_handovers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'distribution_request_id',
        'fiscal_year_id',
        'office_id',
        'prefix',
        'distribution_handover_number',
        'district_id',
        'local_level_id',
        'project_code_id',
        'to_name',
        'letter_body',
        'cc_name',
        'health_facility_name',
        'remarks',
        'total_amount',
        'date_of_handover',
        'approver_id',
        'status_id',
        'receiver_id',
        'received_date',
        'receiver_remarks',
        'handover_date',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['received_date', 'handover_date'];

    /**
     * Get the approved log for the distribution handover.
     */
    public function approvedLog()
    {
        return $this->hasOne(DistributionHandoverLog::class, 'distribution_handover_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))->latest();
    }

    /**
     * Get the approver of a distribution
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the receiver of a distribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->withDefault();
    }

    /**
     * Get the distribution handover items for the distribution handover.
     */
    public function distributionHandoverItems()
    {
        return $this->hasMany(DistributionHandoverItem::class, 'distribution_handover_id');
    }

    /**
     * Get the distribution request that owns the distribution handover.
     */
    public function distributionRequest()
    {
        return $this->belongsTo(DistributionRequest::class, 'distribution_request_id');
    }

    public function healthFacility()
    {
        return $this->hasOneThrough(HealthFacility::class, DistributionRequest::class, 'id', 'id', 'distribution_request_id', 'health_facility_id');
    }

    /**
     * Get the district of the distribution handover.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the local level of the distribution handover.
     */
    public function localLevel()
    {
        return $this->belongsTo(LocalLevel::class, 'local_level_id')->withDefault();
    }

    /**
     * Get the logs for the distribution handover.
     */
    public function logs()
    {
        return $this->hasMany(DistributionHandoverLog::class, 'distribution_handover_id');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the project of the distribution handover.
     */
    public function projectCode()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    /**
     * Get requester of the distribution handover.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the distribution handover status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApprovedDate()
    {
        return $this->approvedLog ? $this->approvedLog->created_at->format('M d, Y') : '';
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get receiver name
     *
     * @return mixed
     */
    public function getReceiverName()
    {
        return $this->receiver->getFullName();
    }

    public function getDistributionHandoverNumber()
    {
        return $this->prefix.$this->distribution_handover_number;
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getLocalLevelName()
    {
        return $this->localLevel->getLocalLevelName();
    }

    public function getProvinceName()
    {
        return $this->district->getProvinceName();
    }

    public function getProjectCode()
    {
        return $this->projectCode->getProjectCodeWithDescription();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function receivedLog()
    {
        return $this->hasOne(DistributionHandoverLog::class, 'distribution_handover_id')
            ->whereStatusId(config('constant.RECEIVED_STATUS'))
            ->latest();
    }

    public function distributedLog()
    {
        return $this->hasOne(DistributionHandoverLog::class, 'distribution_handover_id')
            ->whereStatusId(config('constant.DISTRIBUTED_STATUS'))
            ->latest();
    }

    public function getReceivedDate()
    {
        return $this->received_date?->format('Y-m-d');
    }

    /**
     * Get the get the handover received date of the distribution handover.
     *
     * @return string
     */
   public function getHandoverDate()
    {
        return $this->handover_date?->format('Y-m-d');
    }
}
