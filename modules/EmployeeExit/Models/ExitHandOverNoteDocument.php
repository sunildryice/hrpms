<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Master\Models\ActivityCode;

class ExitHandOverNoteDocument extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_handover_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'handover_note_id',
        'attachment_type',
        'attachment_name',
        'attachment', 
       
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * Get the activityCode of the purchase order item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }


    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

  
    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }


    public function getAttachment()
    {
        return $this->attachment ? true : false;
    }


    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(AdvanceRequestLog::class, 'advance_request_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(AdvanceRequestLog::class, 'advance_request_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }


    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    /**
     * Get the advance items for the advance request.
     */
    public function advanceRequestDetails()
    {
        return $this->hasMany(AdvanceRequestDetail::class, 'advance_request_id');
    }

    public function getEstimatedAmount()
    {
        return $this->advanceRequestDetails->sum('amount');
    }

    public function getAdvanceRequestNumber()
    {
        return $this->prefix . $this->advance_number;
    }

    public function getRequestDate()
    {
        return $this->request_date->toFormattedDateString();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getRequiredDate()
    {
        return $this->required_date->toFormattedDateString();
    }


}
