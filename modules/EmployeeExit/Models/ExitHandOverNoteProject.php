<?php

namespace Modules\EmployeeExit\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;

class ExitHandOverNoteProject extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_handover_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'handover_note_id',
        'project',
        'project_code_id',
        'project_status',
        'action_needed', // text
        'partners',
        'budget',
        'critical_issues',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function handOverNote()
    {
        return $this->belongsTo(ExitHandOverNote::class, 'handover_note_id')->withDefault();
    }
    /**
     * Get the project codes.
     */
    public function projectCodes()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    public function getProjectCode()
    {
        return $this->projectCodes->title;
    }

    public function getProject()
    {
        return $this->project ?: $this->getProjectCode();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
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
