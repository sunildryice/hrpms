<?php

namespace Modules\DistributionRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\HealthFacility;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class DistributionRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'distribution_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'district_id',
        'project_code_id',
        'prefix',
        'distribution_request_number',
        'health_facility_name',
        'health_facility_id',
        'remarks',
        'total_amount',
        'reviewer_id',
        'approver_id',
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

    protected $dates = [];

    /**
     * Get the approver of a distribution
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the distribution handover for the distribution request.
     */
    public function distributionHandover()
    {
        return $this->hasOne(DistributionHandover::class, 'distribution_request_id');
    }

    /**
     * Get the distribution request items for the distribution request.
     */
    public function distributionRequestItems()
    {
        return $this->hasMany(DistributionRequestItem::class, 'distribution_request_id');
    }

    /**
     * Get the district of the distribution request.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    public function healthFacility()
    {
        return $this->belongsTo(HealthFacility::class, 'health_facility_id')->withDefault();
    }

    /**
     * Get the logs for the distribution request.
     */
    public function logs()
    {
        return $this->hasMany(DistributionRequestLog::class, 'distribution_request_id');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the project of the distribution request.
     */
    public function projectCode()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }


    /**
     * Get requester of the distribution request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the distribution request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getDistributionRequestNumber()
    {
        return $this->prefix . $this->distribution_request_number;
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getProjectCode()
    {
        return $this->projectCode->getProjectCodeWithDescription();
    }

    public function getProjectCodeShortName()
    {
        return $this->projectCode->getShortName();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getHealthFacility()
    {
        return $this->healthFacility->getTitle();
    }

    public function getHealthFacilityDesc()
    {
        return $this->healthFacility->getTitle() .', '. $this->healthFacility->getDistrict();
    }
}
