<?php

namespace Modules\ConstructionTrack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;
use Modules\ConstructionTrack\Models\Construction;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\ConstructionTrack\Models\ConstructionProgressAttachment;

class ConstructionProgress extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'construction_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'construction_id',
        'report_date',
        // 'work_start_date',
        // 'work_completion_date',
        'progress_percentage',
        'attachment',
        'estimate',
        'remarks',
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

    protected $dates = [
        'report_date',
        // 'work_start_date',
        // 'work_completion_date'
    ];


    public function attachments()
    {
        return $this->hasMany(ConstructionProgressAttachment::class, 'construction_progress_id');
    }

    /**
     * Get the approver of a construction
     */
    public function construction()
    {
        return $this->belongsTo(Construction::class, 'construction_id')->withDefault();
    }

    /**
     * Get the district of the employee.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

       public function getDistrictName()
    {
        return $this->district->district_name;
    }


    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get requester of the advance request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the advance status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(ConstructionLog::class, 'construction_progress_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(ConstructionLog::class, 'construction_progress_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }


    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    //  /**
    //  * Get the advance items for the advance request.
    //  */
    // public function advanceRequestDetails()
    // {
    //     return $this->hasMany(AdvanceRequestDetail::class, 'advance_request_id');
    // }

    // public function getEstimatedAmount()
    // {
    //     return $this->advanceRequestDetails->sum('amount');
    // }

    // public function getAdvanceRequestNumber()
    // {
    //     return $this->prefix . $this->advance_number;
    // }

    // public function getRequestDate()
    // {
    //     return $this->request_date->toFormattedDateString();
    // }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    // public function getRequiredDate()
    // {
    //     return $this->required_date->toFormattedDateString();
    // }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getAttachments()
    {
        return $this->attachments()->get();
    }


}
