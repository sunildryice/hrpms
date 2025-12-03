<?php

namespace Modules\Employee\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Department;
use Modules\Master\Models\Designation;
use Modules\Master\Models\District;
use Modules\Master\Models\Office;

class Tenure extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_tenures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'designation_id',
        'department_id',
        'supervisor_id',
        'cross_supervisor_id',
        'next_line_manager_id',
        'office_id',
        'duty_station',
        'duty_station_id',
        'joined_date',
        'to_date',
        'contract_end_date',
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

    protected $dates = ['joined_date', 'to_date', 'contract_end_date'];

    /**
     * Get the employee of the tenure.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the designation of the tenure.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withDefault();
    }

    /**
     * Get the department of the tenure.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    /**
     * Get the office of the tenure.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the supervisor of the tenure.
     */
    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id')->withDefault();
    }

    /**
     * Get the cross supervisor of the tenure.
     */
    public function crossSupervisor()
    {
        return $this->belongsTo(Employee::class, 'cross_supervisor_id')->withDefault();
    }

    /**
     * Get the next line manager of the tenure.
     */
    public function nextLineManager()
    {
        return $this->belongsTo(Employee::class, 'next_line_manager_id')->withDefault();
    }

    public function nextTenure()
    {
        return $this->hasOne(Tenure::class, 'employee_id', 'employee_id')
            ->where('joined_date', '>', $this->joined_date)
            ->orderBy('joined_date', 'asc');
    }

    /**
     * Get the district of duty station of the tenure.
     */
    public function dutyStation()
    {
        return $this->belongsTo(District::class, 'duty_station_id')->withDefault();
    }

    public function getDepartmentName()
    {
        return $this->department?->getDepartmentName();
    }

    public function getDesignationName()
    {
        return $this->designation?->getDesignationName();
    }

    public function getSupervisorName()
    {
        return $this->supervisor->getFullName();
    }

    public function getSupervisorDesignation()
    {
        return $this->supervisor->latestTenure->getDesignationName();
    }

    public function getCrossSupervisorName()
    {
        return $this->crossSupervisor->getFullName();
    }

    public function getCrossSupervisorDesignation()
    {
        return $this->crossSupervisor->getDesignationName();
    }

    public function getNextLineManagerName()
    {
        return $this->nextLineManager->getFullName();
    }

    public function getDutyStation()
    {
        return $this->dutyStation->getDistrictName();
    }

    public function getJoinedDate()
    {
        return $this->joined_date ? $this->joined_date->toFormattedDateString() : '';
    }

    public function getContractEndDate()
    {
        return $this->contract_end_date ? $this->contract_end_date->toFormattedDateString() : '';
    }

    public function getFormattedContractEndDate()
    {
        return $this->contract_end_date ? $this->contract_end_date->format('Y-m-d') : '';
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getToDate(): ?string
    {
        return $this->to_date?->format('M d, Y') ?: $this->nextTenure?->joined_date?->format('M d, Y') ?: 'Present';
    }

    public function getFormattedToDate(): ?string
    {
        return $this->to_date?->format('Y-m-d') ?: $this->nextTenure?->joined_date?->format('Y-m-d') ?: '';
    }
}
