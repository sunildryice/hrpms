<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\ExitQuestion;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Employee\Models\Employee;

class ExitInterviewAnswer extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_interview_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exit_interview_id',
        'question_id',
        'answer',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    protected $dates = ['required_date', 'request_date'];

     public function exitQuestionsAnswer()
     {
         return $this->belongsTo(ExitQuestion::class,'question_id');
     }

    /**
     * Get requester of the leave request.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }
     public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }



    /**
     * Get the approver of a advance
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
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

    // /**
    //  * Get requester of the advance request.
    //  */
    // public function requester()
    // {
    //     return $this->belongsTo(User::class, 'requester_id')->withDefault();
    // }

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
        return $this->hasMany(ExitHandOverNoteLog::class, 'handover_note_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(ExitHandOverNoteLog::class, 'handover_note_id')
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

    // public function getRequesterName()
    // {
    //     return $this->requester->getFullName();
    // }

    public function getRequiredDate()
    {
        return $this->required_date->toFormattedDateString();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }


}
