<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Department;
use Modules\Master\Models\Designation;
use Modules\Master\Models\District;
use Modules\Master\Models\Office;

class EmployeeHour extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'work_percentile',
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

    protected $dates = ['start_date', 'end_date'];

    /**
     * Get the employee of the tenure.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getStartDate()
    {
        return $this->start_date ? $this->start_date->toFormattedDateString() : "";
    }

    public function getEndDate()
    {
        return $this->end_date ? $this->end_date->toFormattedDateString() : "";
    }
}
