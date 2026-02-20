<?php

namespace Modules\Employee\Models;

use DateTime;
use App\Traits\ModelEventLogger;
use Modules\Master\Models\Gender;
use Modules\Master\Models\Office;
use Illuminate\Support\Collection;
use Modules\Privilege\Models\User;
use Modules\Master\Models\Department;
use Modules\Master\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\EmployeeType;
use Modules\Master\Models\MaritalStatus;
use Modules\GoodRequest\Models\GoodRequest;
use Modules\LeaveRequest\Models\LeaveEncash;
use Modules\EmployeeExit\Models\ExitInterview;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\ConstructionTrack\Models\Construction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\ProbationaryReview\Models\ProbationaryReview;

class Employee extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_id',
        'designation_id',
        'department_id',
        'employee_type_id',
        'employee_code',
        'ste_code',
        'supervisor_id',
        'cross_supervisor_id',
        'next_line_manager_id',
        'full_name',
        'official_email_address',
        'personal_email_address',
        'mobile_number',
        'marital_status',
        'gender',
        'citizenship_number',
        'pan_number',
        'citizenship_attachment',
        'pan_attachment',
        'signature',
        'profile_picture',
        'cv_attachment',
        'date_of_birth',
        'joined_date',
        'probation_complete_date',
        'last_working_date',
        'religion_id',
        'caste_id',
        'nid_number',
        'passport_number',
        'passport_attachment',
        'vehicle_license_number',
        'vehicle_license_category',
        'created_by',
        'updated_by',
        'activated_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'joined_date',
        'probation_completion_date',
        'last_working_date',
        'activated_at',
    ];

    protected $casts = [
        'vehicle_license_category' => 'array',
    ];

    /**
     * Get the address of the employee.
     */

    public function requestId(): Attribute
    {
        return Attribute::make(
            get: fn() => 'HI-EMP-' . sprintf('%04d', $this->employee_code)
        );
    }

    public function requestSTEId(): Attribute
    {
        return Attribute::make(
            get: fn() => 'HI-STE-' . sprintf('%04d', $this->ste_code)
        );
    }

    public function consultantLeave()
    {
        return $this->hasOne(ConsultantLeave::class, 'employee_id', 'id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'employee_id')->withDefault();
    }

    public function exitInterview()
    {
        return $this->hasOne(ExitInterview::class, 'employee_id')->withDefault()->latest();
    }

    public function exitInterviews()
    {
        return $this->hasMany(ExitInterview::class, 'employee_id');
    }

    public function goodRequests()
    {
        return $this->belongsToMany(GoodRequest::class, 'good_request_employees', 'employee_id', 'good_request_id');
    }

    public function constructions()
    {
        return $this->hasMany(Construction::class, 'engineer_id');
    }

    // Employee
    public function genderName()
    {
        return $this->belongsTo(Gender::class, 'gender', 'id')->withDefault();
    }

    /**
     * Get the department of the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    /**
     * Get the designation of the employee.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withDefault();
    }

    /**
     * Get the educational details of the employee.
     */
    public function education()
    {
        return $this->hasMany(Education::class, 'employee_id');
    }

    /**
     * Get the experiences of the employee.
     */
    public function experiences()
    {
        return $this->hasMany(Experience::class, 'employee_id');
    }

    /**
     * Get the employee exit hand over note of the employee.
     */
    public function exitHandOverNote()
    {
        return $this->hasOne(ExitHandOverNote::class, 'employee_id')->withDefault();
    }

    /**
     * Get the family details of the employee.
     */
    public function familyDetails()
    {
        return $this->hasMany(FamilyDetail::class, 'employee_id');
    }

    /**
     * Get the finance of the employee.
     */
    public function finance()
    {
        return $this->hasOne(Finance::class, 'employee_id')->withDefault();
    }

    /**
     * Get the gender of the employee.
     */
    public function employeeGender()
    {
        return $this->belongsTo(Gender::class, 'gender')->withDefault();
    }

    /**
     * Get the first tenure of the employee.
     */
    public function firstTenure()
    {
        return $this->hasOne(Tenure::class, 'employee_id')
            // ->first()
            ->withDefault();
    }

    public function firstHour()
    {
        return $this->hasOne(EmployeeHour::class, 'employee_id')
            ->withDefault();
    }

    /**
     * Get the insurances of the employee.
     */
    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'employee_id');
    }

    /**
     * Get the latest tenure of the employee.
     */
    public function latestTenure()
    {
        return $this->hasOne(Tenure::class, 'employee_id')
            ->latest()
            ->withDefault();
    }

    public function latestHour()
    {
        return $this->hasOne(EmployeeHour::class, 'employee_id')
            ->latest()
            ->withDefault();
    }

    /**
     * Get the leaves of the employee.
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status')->withDefault();
    }

    /**
     * Get the medical condition of the employee.
     */
    public function medicalCondition()
    {
        return $this->hasOne(MedicalCondition::class, 'employee_id')->withDefault();
    }

    /**
     * Get the nominee of the employee.
     */
    public function nominee()
    {
        return $this->hasOne(FamilyDetail::class, 'employee_id')
            ->whereNotNull('nominee_at')
            ->latest()->withDefault();
    }

    public function emergencyContact()
    {
        return $this->hasOne(FamilyDetail::class, 'employee_id')
            ->whereNotNull('emergency_contact_at')
            ->latest()->withDefault();
    }

    public function spouse()
    {
        return $this->hasOne(FamilyDetail::class, 'employee_id')
            ->whereHas('familyRelation', function ($q) {
                $q->where('title', 'spouse');
            })->latest()->withDefault();
    }

    public function childrens()
    {
        return $this->hasMany(FamilyDetail::class, 'employee_id')
            ->whereHas('familyRelation', function ($q) {
                $q->whereIn('title', ['son', 'daughter']);
            });
    }

    public function leaveEncashments()
    {
        return $this->hasMany(LeaveEncash::class, 'employee_id');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the payment masters of the employee.
     */
    public function paymentMasters()
    {
        return $this->hasMany(PaymentMaster::class, 'employee_id');
    }

    /**
     * Get the probation reviews of the employee.
     */
    public function probationReviews()
    {
        return $this->hasMany(ProbationaryReview::class, 'employee_id');
    }

    /**
     * Get the supervisor of the employee.
     */
    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    /**
     * Get the tenures of the employee.
     */
    public function tenures()
    {
        return $this->hasMany(Tenure::class, 'employee_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get the hours of the employee.
     */
    public function hours()
    {
        return $this->hasMany(EmployeeHour::class, 'employee_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the trainings of the employee.
     */
    public function trainings()
    {
        return $this->hasMany(Training::class, 'employee_id');
    }

    /**
     * Get the user of the employee.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    /**
     * Get the working hours of the employee.
     */
    public function workingHours()
    {
        return $this->hasMany(EmployeeHour::class, 'employee_id')
            ->orderBy('end_date', 'desc');
    }

    public function getUserId()
    {
        return $this->user->id;
    }

    public function performanceReview()
    {
        return $this->hasMany(PerformanceReview::class, 'employee_id');
    }

    /**
     * Get the type of employee
     */
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id')->withDefault();
    }

    public function getActiveStatus()
    {
        return $this->activated_at ? 'Active' : 'Inactive';
    }

    public function getDepartmentName()
    {
        return $this->department->title;
    }

    public function getDesignationName()
    {
        return $this->designation->title;
    }

    public function getDutyStation()
    {
        return $this->latestTenure->getDutyStation();
    }

    public function getFullName()
    {
        return ucfirst($this->full_name);
    }

    public function getEmployeeCode()
    {
        return $this->employee_code;
    }

    public function getFullNameWithCode()
    {
        return ucfirst($this->full_name) . '(' . $this->employee_code . ')';
    }

    public function getFirstJoinedDate()
    {
        return $this->firstTenure->getJoinedDate();
    }

    public function getGender()
    {
        return $this->employeeGender->title;
    }

    public function getMaritalStatus()
    {
        return $this->maritalStatus->title;
    }

    public function isMarried()
    {
        return strtolower($this->maritalStatus->title) == 'married' ? true : false;
    }

    public function isSupervisor()
    {
        return $this->where('supervisor_id', $this->id)
            ->orWhere('cross_supervisor_id', $this->id)
            ->orWhere('next_line_manager_id', $this->id)
            ->count();
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getSupervisorName()
    {
        return ucfirst($this->latestTenure->getSupervisorName());
    }

    public function getDateOfBirth()
    {
        $dob = '';

        if ($this->date_of_birth) {
            $dob = (new DateTime($this->date_of_birth))->format('M d, Y');
        }

        return $dob;
    }
    public function getJoinedDate()
    {
        $joinedDate = '';

        if ($this->joined_date) {
            $joinedDate = (new DateTime($this->joined_date))->format('M d, Y');
        }

        return $joinedDate;
    }

    public function getAge()
    {
        $years = '';
        if ($this->date_of_birth) {
            $currentDate = date('Y-m-d');
            $diff = abs(strtotime($currentDate) - strtotime($this->date_of_birth));
            $years = floor($diff / (365 * 60 * 60 * 24));
        }

        return $years;
    }

    public function getEmployeeType()
    {
        return $this->employeeType->title;
    }

    public function getLeaveBalance($yearId, $leaveTypeId = 0)
    {
        $leave = $this->leaves()->where('fiscal_year_id', $yearId)->where('leave_type_id', $leaveTypeId)->latest()->first();

        return $leave ? $leave->balance . ' ' . $leave->leaveType->getLeaveBasis() : '';
    }

    public function leave()
    {
        return $this->hasOne(Leave::class, 'employee_id');
    }

    public function getLeave($fiscalYear, $leaveType)
    {
        return $this->leave()->where('fiscal_year_id', '=', $fiscalYear)->where('leave_type_id', '=', $leaveType)->firstOrNew();
    }

    public function getNextLineManagerUserId()
    {
        return $this->latestTenure->nextLineManager->id ? $this->latestTenure->nextLineManager->getUserId() : '';
    }

    public function getBankDetail()
    {
        $finance = $this->finance;
        if ($finance) {
            if ($finance->account_number && $finance->bank_name) {
                return $finance->account_number . ' / ' . $finance->bank_name;
            }
        }

        return '';
    }

    public function isConsultant(): bool
    {
        return $this->employee_type_id != config('constant.FULL_TIME_EMPLOYEE');
    }

    public function getGoodRequestAssets(): Collection
    {
        return GoodRequestAsset::query()
            ->where('assigned_user_id', $this->getUserId())
            ->get();
    }

    public function getAssetHandoverStatus(): ?string
    {
        if (
            GoodRequestAsset::query()
                ->where('assigned_user_id', $this->getUserId())
                ->where('handover_status_id', '<>', config('constant.APPROVED_STATUS'))
                ->count()
        ) {
            return null;
        }

        return '<span class="approved badge bg-success">Approved</span>';
    }
}
