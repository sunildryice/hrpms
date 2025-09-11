<?php

namespace Modules\ExitStaffClearance\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\EmployeeExit\Models\EmployeeExitPayable;
use Modules\EmployeeExit\Models\ExitAssetHandover;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\EmployeeExit\Models\ExitInterview;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class StaffClearance extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'exit_staff_clearances';

    protected $fillable = [
        'handover_note_id',
        'supervisor_id',
        'endorser_id',
        'certifier_id',
        'approver_id',
        'verified_at',
        'approved_at',
        'endorsed_at',
        'certified_at',
        'status_id',
    ];

    protected $hidden = [];

    protected $dates = [
        'verified_at',
        'approved_at',
        'endorsed_at',
        'certified_at',
    ];

    public function records()
    {
        return $this->hasMany(StaffClearanceRecord::class, 'staff_clearance_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function endorser()
    {
        return $this->belongsTo(User::class, 'endorser_id')->withDefault();
    }

    public function certifier()
    {
        return $this->belongsTo(User::class, 'certifier_id')->withDefault();
    }

    /**
     * Get the supervisor (verifier)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id')->withDefault();
    }

    public function handoverNote()
    {
        return $this->belongsTo(ExitHandOverNote::class, 'handover_note_id')->withDefault();
    }

    public function employee()
    {
        return $this->hasOneThrough(Employee::class, ExitHandOverNote::class, 'id', 'id', 'handover_note_id', 'employee_id');
    }

    public function employeeExitPayable()
    {
        return $this->hasOneThrough(EmployeeExitPayable::class, ExitHandOverNote::class, 'id', 'handover_note_id', 'handover_note_id', 'id');
    }

    public function exitAssetHandover()
    {
        return $this->hasOneThrough(ExitAssetHandover::class, ExitHandOverNote::class, 'id', 'handover_note_id', 'handover_note_id', 'id');
    }

    public function exitInterview()
    {
        return $this->hasOneThrough(ExitInterview::class, ExitHandOverNote::class, 'id', 'handover_note_id', 'handover_note_id', 'id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    public function keyGoals()
    {
        return $this->hasMany(PerformanceReviewKeyGoal::class, 'staff_clearance_id');
    }

    public function logs()
    {
        return $this->hasMany(StaffClearanceLog::class, 'staff_clearance_id');
    }

    public function reviewType()
    {
        return $this->belongsTo(PerformanceReviewType::class, 'review_type_id')->withDefault();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    public function getLastDutyDate()
    {
        return $this->handoverNote->getLastDutyDate();
    }

    public function getResignationDate()
    {
        return $this->handoverNote->getResignationDate();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getStatus()
    {
        return match (true) {
            $this->status_id == config('constant.VERIFIED2_STATUS') => 'Certified',
            $this->status_id == config('constant.VERIFIED3_STATUS') => 'Endorsed',
            default => ucwords($this->status->title),
        };
        // return $this->status_id == config('constant.VERIFIED2_STATUS') ? 'Certified' : ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getEmployeeTitle()
    {
        return $this->employee->latestTenure->getDesignationName();
    }

    public function getSupervisorName()
    {
        return $this->employee->latestTenure->getSupervisorName();
    }

    public function getEndorserName()
    {
        return $this->endorser->getFullName();
    }

    public function getSupervisorTitle()
    {
        return $this->employee->latestTenure->getSupervisorDesignation();
    }

    public function getTechnicalSupervisorName()
    {
        return $this->employee->latestTenure->getCrossSupervisorName();
    }

    public function getTechnicalSupervisorTitle()
    {
        return $this->employee->latestTenure->getCrossSupervisorDesignation();
    }

    public function getJoinedDate()
    {
        return $this->employee->latestTenure->getJoinedDate();
    }

    public function getLatestRemark()
    {
        return $this->logs->last()->log_remarks;
    }

    public function getDutyStation()
    {
        return $this->employee->latestTenure->getDutyStation();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function recordsFilled(): bool
    {
        return $this->records()->count() == StaffClearanceDepartment::where('parent_id', '<>', '0')->count() && $this->status_id == config('constant.CREATED_STATUS');
    }
}
