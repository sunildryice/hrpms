<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Employee\Models\Employee;

class ExitInterview extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_interviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'approver_id',
        'handover_note_id',
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

    /**
     * Get the approver of exit interview
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

     /**
     * Get created by of the exit Interview.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get employee of exit interview.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    /**
     * Get the exit handover note
     */
    public function exitHandOverNote()
    {
        return $this->belongsTo(ExitHandOverNote::class, 'handover_note_id');
    }

    public function exitInterviewAnswers()
    {
        return $this->hasMany(ExitInterviewAnswer::class, 'exit_interview_id');
    }

    public function exitInterviewRatingAnswers()
    {
        return $this->hasMany(ExitInterviewRatingAnswer::class, 'exit_interview_id');
    }

    public function exitInterviewFeedbackAnswers()
    {
        return $this->hasMany(ExitInterviewFeedbackAnswer::class, 'exit_interview_id');
    }
    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(ExitInterviewLog::class, 'exit_interview_id');
    }

    public function returnedLog()
    {
        return $this->hasOne(ExitInterviewLog::class, 'exit_interview_id')
            ->where('status_id', config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    public function approvedLog()
    {
        return $this->hasOne(ExitInterviewLog::class, 'exit_interview_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function submittedLog()
    {
        return $this->hasOne(ExitInterviewLog::class, 'exit_interview_id')
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the exit interview status.
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
    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

}
