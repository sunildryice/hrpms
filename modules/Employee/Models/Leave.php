<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\EducationLevel;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LeaveType;

class Leave extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_leaves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'fiscal_year_id',
        'leave_type_id',
        'reported_date',
        'opening_balance',
        'earned',
        'taken',
        'paid',
        'lapsed',
        'balance',
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

    protected $dates = ['reported_date'];

    protected $casts = ['reported_date' => 'date:Y-m-d'];

    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id ');
    }

     /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    /**
     * Get the leave type that owns the leave.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * Get the logs for the employee leave.
     */
    public function logs()
    {
        return $this->hasMany(LeaveLog::class, 'employee_leave_id');
    }

    public function getLeaveType()
    {
        return $this->leaveType->title;
    }

    public function getReportedDateMonth()
    {
        return $this->reported_date ?->format('F');
    }

    public function getYearlyLeaves($year)
    {
        $monthlyLeaves = $this->model
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 2);
            })->get();

        $yearlyLeaves = $this->model
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 1);
            })->get();

        $specialLeaves = $this->model
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->whereNotIn('leave_frequency', [1, 2]);
            })->get();

        $leaves = $monthlyLeaves->merge($yearlyLeaves)->merge($specialLeaves);
        return $leaves;
    }
}
