<?php

namespace App\Providers;

use Carbon\Carbon;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;
use Illuminate\Support\Facades\Gate;
use Modules\Employee\Models\Address;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\WorkPlan;
use Modules\Employee\Models\Employee;
use Modules\Project\Models\TimeSheet;
use Modules\Privilege\Models\Permission;
use Modules\Employee\Policies\AddressPolicy;
use Modules\Project\Policies\ProjectActivityPolicy;
use Modules\Project\Policies\WorkPlanPolicy;
use Modules\Inventory\Models\Asset;
use Modules\Inventory\Policies\AssetPolicy;
use Modules\Employee\Policies\EmployeePolicy;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\TravelRequest\Models\LocalTravel;
use Modules\TravelRequest\Models\TravelClaim;
use Modules\WorkFromHome\Models\WorkFromHome;
use Modules\EmployeeExit\Models\ExitInterview;
use Modules\LieuLeave\Models\LieuLeaveRequest;
use Modules\TravelRequest\Models\TravelReport;
use Modules\LieuLeave\Policies\LieuLeavePolicy;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\TravelRequest\Models\TravelRequestView;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\EmployeeRequest\Models\EmployeeRequest;
use Modules\EventCompletion\Models\EventCompletion;
use Modules\Project\Policies\MonthlyTimesheetPolicy;
use Modules\EmployeeExit\Policies\ExitHandOverPolicy;
use Modules\LeaveRequest\Policies\LeaveRequestPolicy;
use Modules\TravelRequest\Policies\LocalTravelPolicy;
use Modules\TravelRequest\Policies\TravelClaimPolicy;
use Modules\WorkFromHome\Policies\WorkFromHomePolicy;
use Modules\EmployeeExit\Policies\ExitInterViewPolicy;
use Modules\TravelRequest\Policies\TravelReportPolicy;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\TravelRequest\Policies\TravelRequestPolicy;
use Modules\EmployeeAttendance\Policies\AttendancePolicy;
use Modules\EmployeeRequest\Policies\EmployeeRequestPolicy;
use Modules\EventCompletion\Policies\EventCompletionPolicy;
use Modules\PerformanceReview\Policies\PerformanceReviewPolicy;
use Modules\Project\Repositories\ActivityUpdatePeriodRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Address::class => AddressPolicy::class,
        Asset::class => AssetPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Employee::class => EmployeePolicy::class,
        EmployeeRequest::class => EmployeeRequestPolicy::class,
        EventCompletion::class => EventCompletionPolicy::class,
        ExitInterview::class => ExitInterViewPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        LieuLeaveRequest::class => LieuLeavePolicy::class,
        LocalTravel::class => LocalTravelPolicy::class,
        PerformanceReview::class => PerformanceReviewPolicy::class,
        ProjectActivity::class => ProjectActivityPolicy::class,
        TimeSheet::class => MonthlyTimesheetPolicy::class,
        TravelClaim::class => TravelClaimPolicy::class,
        TravelReport::class => TravelReportPolicy::class,
        TravelRequest::class => TravelRequestPolicy::class,
        TravelRequestView::class => TravelRequestPolicy::class,
        WorkFromHome::class => WorkFromHomePolicy::class,
        WorkPlan::class => WorkPlanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();


        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            Gate::define($permission->guard_name, function ($user) use ($permission) {
                return in_array($permission->guard_name, session()->get('access_permissions', []));
            });
        }

        Gate::define('approve-advance-settlement-form', function ($user) {
            return $user->can('approve-advance-request') || $user->can('approve-recommended-advance-settlement');
        });

        Gate::define('approve-payment-sheet-form', function ($user) {
            return $user->can('approve-payment-sheet') || $user->can('approve-recommended-payment-sheet');
        });

        Gate::define('approve-purchase-request-form', function ($user) {
            return $user->can('approve-purchase-request') || $user->can('approve-recommended-purchase-request');
        });

        Gate::define('approve-travel-form', function ($user) {
            return $user->can('approve-travel-request') || $user->can('approve-recommended-travel-request');
        });

        Gate::define('approve-event-form', function ($user) {
            return $user->can('approve-event-completion') || $user->can('approve-recommended-event-completion');
        });

        Gate::define('manage-project-activity-on-certain-time', function (User $user, ?Project $project = null) {

            $checkCurrentActivePeriod = app(ActivityUpdatePeriodRepository::class)->checkCurrentActivePeriod();

            return ($project->isFocalPerson($user->id) || $project->isTeamLead($user->id)) && $checkCurrentActivePeriod;
        });

        Gate::define('manage-project-activity-project-admin', function (User $user, ?Project $project = null) {

            $checkCurrentActivePeriod = app(ActivityUpdatePeriodRepository::class)->checkCurrentActivePeriod();

            return ($user->employee?->employee_code == 62) && $checkCurrentActivePeriod;
        });

        Gate::define('manage-project-activity-other-detail', function (User $user, ?Project $project = null) {

            return ($project->isFocalPerson($user->id) || $project->isTeamLead($user->id) || $project->isActivityMember($user->id) || $user->employee?->employee_code == 62);
        });

        Gate::define('project-is-active', function (User $user, ?Project $project = null) {
            if (!$project) {
                return false;
            }
            return $project->activated_at !== null;
        });

        Gate::define('project-is-ongoing', function (User $user, ?Project $project = null) {
            if (!$project) {
                return false;
            }
            return !$project->completion_date || $project->completion_date->gte(now()->startOfDay());
        });
    }
}
