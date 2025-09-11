<?php

namespace Modules\EmployeeExit\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EmployeeExit\Models\ExitHandOverNoteActivity;
use Modules\EmployeeExit\Models\ExitHandOverNoteProject;
use Modules\Employee\Models\Employee;
use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class ExitHandOverNote extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_handover_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'approver_id',
        'last_duty_date',
        'resignation_date',
        'is_insurance',
        'duty_description',
        'reporting_procedures',
        'meeting_description',
        'contact_after_exit',
        'status_id',
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

    protected $dates = ['last_duty_date', 'resignation_date'];

    public function handoverProjects()
    {
        return $this->hasMany(ExitHandOverNoteProject::class, 'handover_note_id');
    }

    public function handoverActivities()
    {
        return $this->hasMany(ExitHandOverNoteActivity::class, 'handover_note_id');
    }
    /**
     * Get the approver of handover
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function staffClearance()
    {
        return $this->hasOne(StaffClearance::class, 'handover_note_id');
    }

    /**
     * Get created by of the handover.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get requester of the handover request.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    /**
     * Get the employee exit payable
     */
    public function employeeExitPayable()
    {
        return $this->hasOne(EmployeeExitPayable::class, 'handover_note_id');
    }

    /**
     * Get the exit interview
     */
    public function exitInterview()
    {
        return $this->hasOne(ExitInterview::class, 'handover_note_id')->withDefault();
    }

    /**
     * Get Exit asset handover
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function exitAssetHandover()
    {
        return $this->hasOne(ExitAssetHandover::class, 'handover_note_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }
    /**
     * Get the logs for the handover request.
     */
    public function logs()
    {
        return $this->hasMany(ExitHandOverNoteLog::class, 'handover_note_id');
    }

    public function approvedLog()
    {
        return $this->hasOne(ExitHandoverNoteLog::class, 'handover_note_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))->latest();
    }

    public function submittedLog()
    {
        return $this->hasOne(ExitHandoverNoteLog::class, 'handover_note_id')
            ->where('status_id', config('constant.SUBMITTED_STATUS'))->latest();
    }

    /**
     * Get the handover status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getInsuranceStatus()
    {
        return $this->is_insurance == 1 ? 'Y' : 'N';
    }

    public function getLastDutyDate()
    {
        return $this->last_duty_date?->toFormattedDateString();
    }

    public function getResignationDate()
    {
        return $this->resignation_date?->toFormattedDateString();
    }

    public function getResignationYear()
    {
        return $this->resignation_date?->format('Y');
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function exitHandoverSubmitted()
    {
        return $this->logs->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])->last() ? true : false;
    }

    public function exitInterviewSubmitted()
    {
        return $this->exitInterview->logs->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])->last() ? true : false;
    }

    public function exitPayableSubmitted()
    {
        return $this->employeeExitPayable->logs->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])->last() ? true : false;
    }
}
