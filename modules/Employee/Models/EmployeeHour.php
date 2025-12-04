<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Carbon\Carbon;
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
        'start_time',
        'end_time',
        // 'work_percentile',
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

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
    ];

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

    public function getStartTime()
    {
        return Carbon::parse($this->start_time)->format('g:i A');
    }

    public function getEndTime()
    {
        return Carbon::parse($this->end_time)->format('g:i A');
    }

    public function getStartTimeAttribute($value)
    {
        return $value ? date('g:i A', strtotime($value)) : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? date('g:i A', strtotime($value)) : null;
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }
}
