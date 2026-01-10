<?php

namespace Modules\Privilege\Models;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Traits\ModelEventLogger;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Modules\Employee\Models\Employee;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Modules\LeaveRequest\Models\LeaveEncash;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\Master\Models\Department;
use Modules\Master\Models\Office;
use Modules\Master\Repositories\OfficeRepository;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\TravelRequest\Models\TravelRequest;
use Illuminate\Support\Str;
use Modules\Project\Models\Project;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, ModelEventLogger, Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'full_name',
        'email_address',
        'password',
        'remember_token',
        'verify_token',
        'reset_token',
        'activated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];


    public function routeNotificationFor($driver, $notification = null)
    {
        if (method_exists($this, $method = 'routeNotificationFor' . Str::studly($driver))) {
            return $this->{$method}($notification);
        }

        return match ($driver) {
            'database' => $this->notifications(),
            'mail' => $this->email_address,
            default => null,
        };
    }


    /*
     * Get activity logs of the user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /*
     * Get department of the user
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /*
     * Get employee of the user
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    /*
     * Get audit logs of the user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /*
     * Get office of the user
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function goodRequestAssets()
    {
        return $this->hasMany(GoodRequestAsset::class, 'assigned_user_id');
    }
    /*
     * Get permissions of the user
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /*
     * Get roles of the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'requester_id');
    }

    public function leaveEncashments()
    {
        return $this->hasMany(LeaveEncash::class, 'requester_id');
    }

    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'requester_id')
            ->with(['leaveDays' => function ($q) {
                $q->where('leave_duration', '>', 0);
            }])->where('status_id', config('constant.APPROVED_STATUS'));
    }

    public function getApprovedLeaveRequests()
    {
        return $this->approvedLeaveRequests;
    }

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'requester_id');
    }

    public function approvedTravelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'requester_id')
            ->where('status_id', config('constant.APPROVED_STATUS'));
    }

    public function getApprovedTravelRequests()
    {
        return $this->approvedTravelRequests;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function getDepartmentName()
    {
        return $this->department ? $this->department->department_name : "";
    }

    public function getEmployeeCode()
    {
        return $this->employee ? $this->employee->employee_code : "";
    }

    public function getEmployeeName()
    {
        return $this->employee ? $this->employee->getFullName() : "";
    }

    public function getFullName()
    {
        return ucfirst($this->full_name);
    }

    public function getFullNameWithEmpCode()
    {
        return $this->getFullName() . ' (' . $this->getEmployeeCode() . ')';
    }

    public function getOfficeName()
    {
        return $this->employee ? $this->employee->getOfficeName() : "";
    }

    public function getRoles()
    {
        return $this->roles()->pluck('role');
    }

    public function getRolesName()
    {
        return $this->roles ? implode(', ', $this->roles->pluck('role')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where('role', $role)->first();
    }

    /**
     * Check multiple roles
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('role', $roles)->first();
    }

    public function isDeveloper()
    {
        return in_array(1, $this->roles()->pluck('role_id')->toArray());
    }

    public function isHandoverNoteExists()
    {
        return $this->employee->exitHandOverNote->id;
    }

    public function isProbationExists()
    {
        return $this->employee->probationReviews->count();
    }

    public function isSupervisor()
    {
        return $this->employee->isSupervisor();
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'requester_id');
    }

    public function performanceReviewExists()
    {
        return $this->performanceReviews->count() == 0 ? false : true;
    }

    /**
     * Array of all the office ids which is accessible to the user based on the office type the user belongs to.
     * @return mixed
     */
    public function getAccessibleOfficesIds()
    {
        $officeRepository = app(OfficeRepository::class);

        $latestTenure = $this->employee->latestTenure;
        $employeeCurrentOffice = $officeRepository->findOrNull($latestTenure->office_id);

        if (!$employeeCurrentOffice) {
            return [];
        }

        $officeTypes = [
            'head_office' => 1,
            'cluster' => 2,
            'district' => 3,
        ];

        if ($employeeCurrentOffice->office_type_id == $officeTypes['head_office']) {
            $offices = $officeRepository->getOffices();
            return $offices->pluck('id')->toArray();
        } elseif ($employeeCurrentOffice->office_type_id == $officeTypes['cluster']) {
            $offices = $employeeCurrentOffice->childrens->push($employeeCurrentOffice);
            return $offices->pluck('id')->toArray();
        } elseif ($employeeCurrentOffice->office_type_id == $officeTypes['district']) {
            $officeIds[] = $employeeCurrentOffice->id;
            return $officeIds;
        }
    }

    public function getCurrentOffice()
    {
        $office = $this->employee->latestTenure->office;
        if (!$office) {
            return null;
        }
        return $office;
    }

    public function hasAnyPermission(...$permissions)
    {
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                $hasPermission = true;
            }
        }
        return $hasPermission;
    }
}
