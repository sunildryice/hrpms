<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Employee\Models\Employee;

class EmployeeExitPayable extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_exit_payables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'handover_note_id',
        'approver_id',
        'salary_date_from',
        'salary_date_to',
        'leave_balance',
        'salary_amount',
        'festival_bonus',
        'festival_bonus_date_from',
        'festival_bonus_date_to',
        'gratuity_amount',
        'other_amount',
        'advance_amount',
        'loan_amount',
        'other_payable_amount',
        'deduction_amount',
        'remarks',
        'reviewer_id',
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

    protected $dates = ['festival_bonus_date_from', 'festival_bonus_date_to','salary_date_from', 'salary_date_to'];


    /**
     * Get the approver of a advance
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the created by
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get employee of exit payable.
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

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

     /**
     * Get the logs for the exit payable request.
     */
    public function logs()
    {
        return $this->hasMany(EmployeeExitPayableLog::class, 'exit_payable_id');
    }

     /**
     * Get reviewer of the exit payable.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the exit payable status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedByName()
    {
        return $this->createdBy->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

      public function getRequestedDate()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function getUpdatedDate()
    {
         return $this->updated_at->toFormattedDateString();
    }

}
