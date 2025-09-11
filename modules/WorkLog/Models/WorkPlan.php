<?php

namespace Modules\WorkLog\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Employee\Models\Employee;
use Modules\Master\Models\Status;
use Modules\WorkLog\Models\WorkPlanDailyLog;
use Modules\WorkLog\Models\WorkPlanLogs;
use Modules\Privilege\Models\User;

class WorkPlan extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'work_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'designation_id',
        'year',
        'month',
        'summary',
        'planned',
        'completed',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'status_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the approver of a worklog
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the daily work plans of by work plan id.
     */
    public function workPlanDailyLog()
    {
        return $this->hasMany(WorkPlanDailyLog::class, 'work_plan_id');
    }

    /**
     * Get the logs for the work plan.
     */
    public function logs()
    {
        return $this->hasMany(WorkPlanLogs::class, 'work_plan_id');
    }

     /**
     * Get the requester of a training request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

     /**
     * Get the work plan status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApprover()
    {
        return $this->approver->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->full_name;
    }

    public function getMonth(){
        return date('F', mktime(0, 0, 0, $this->month, 10));
    }

    public function getRequester()
    {
        return $this->requester->getFullName();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getYearMonth(){
        return $this->year.'-'.date('F', mktime(0, 0, 0, $this->month, 10));
    }

    public function getLastDayOfMonth()
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->lastOfMonth();
    }


}
