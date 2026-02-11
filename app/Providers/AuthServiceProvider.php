<?php

namespace App\Providers;

use Carbon\Carbon;
use Modules\Grn\Models\Grn;
use Modules\Grn\Models\GrnItem;
use Modules\Privilege\Models\User;
use Modules\Grn\Policies\GrnPolicy;
use Modules\Project\Models\Project;
use Illuminate\Support\Facades\Gate;
use Modules\Employee\Models\Address;
use Modules\Project\Models\WorkPlan;
use Modules\Employee\Models\Employee;
use Modules\Project\Models\TimeSheet;
use Modules\Grn\Policies\GrnItemPolicy;
use Modules\Privilege\Models\Permission;
use Modules\Employee\Policies\AddressPolicy;
use Modules\Project\Policies\WorkPlanPolicy;
use Modules\AdvanceRequest\Models\Settlement;
use Modules\Employee\Policies\EmployeePolicy;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\TravelRequest\Models\LocalTravel;
use Modules\TravelRequest\Models\TravelClaim;
use Modules\WorkFromHome\Models\WorkFromHome;
use Modules\EmployeeExit\Models\ExitInterview;
use Modules\LieuLeave\Models\LieuLeaveRequest;
use Modules\TravelRequest\Models\TravelReport;
use Modules\LieuLeave\Policies\LieuLeavePolicy;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\ConstructionTrack\Models\Construction;
use Modules\EmployeeRequest\Models\EmployeeRequest;
use Modules\EventCompletion\Models\EventCompletion;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use Modules\Project\Policies\MonthlyTimesheetPolicy;
use Modules\EmployeeExit\Policies\ExitHandOverPolicy;
use Modules\LeaveRequest\Policies\LeaveRequestPolicy;
use Modules\TravelRequest\Policies\LocalTravelPolicy;
use Modules\TravelRequest\Policies\TravelClaimPolicy;
use Modules\WorkFromHome\Policies\WorkFromHomePolicy;
use Modules\EmployeeExit\Policies\ExitInterViewPolicy;
use Modules\TravelRequest\Policies\TravelReportPolicy;
use Modules\ConstructionTrack\Models\ConstructionParty;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PurchaseOrder\Policies\PurchaseOrderPolicy;
use Modules\TravelRequest\Policies\TravelRequestPolicy;
use Modules\EmployeeAttendance\Policies\AttendancePolicy;
use Modules\ConstructionTrack\Policies\ConstructionPolicy;
use Modules\EmployeeRequest\Policies\EmployeeRequestPolicy;
use Modules\EventCompletion\Policies\EventCompletionPolicy;
use Modules\PurchaseRequest\Policies\PurchaseRequestPolicy;
use Modules\AdvanceRequest\Policies\SettlementAdvancePolicy;
use Modules\ConstructionTrack\Models\ConstructionInstallment;
use Modules\ConstructionTrack\Policies\ConstructionPartyPolicy;
use Modules\PerformanceReview\Policies\PerformanceReviewPolicy;
use Modules\Project\Repositories\ActivityUpdatePeriodRepository;
use Modules\ConstructionTrack\Policies\ConstructionInstallmentPolicy;
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
        Employee::class => EmployeePolicy::class,
        ExitInterview::class => ExitInterViewPolicy::class,
        Grn::class => GrnPolicy::class,
        GrnItem::class => GrnItemPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        LocalTravel::class => LocalTravelPolicy::class,
        PurchaseRequest::class => PurchaseRequestPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Settlement::class => SettlementAdvancePolicy::class,
        TravelClaim::class => TravelClaimPolicy::class,
        TravelReport::class => TravelReportPolicy::class,
        TravelRequest::class => TravelRequestPolicy::class,
        Construction::class => ConstructionPolicy::class,
        Attendance::class => AttendancePolicy::class,
        PerformanceReview::class => PerformanceReviewPolicy::class,
        ConstructionParty::class => ConstructionPartyPolicy::class,
        ConstructionInstallment::class => ConstructionInstallmentPolicy::class,
        EmployeeRequest::class => EmployeeRequestPolicy::class,
        EventCompletion::class => EventCompletionPolicy::class,
        WorkFromHome::class => WorkFromHomePolicy::class,
        WorkPlan::class => WorkPlanPolicy::class,
        TimeSheet::class => MonthlyTimesheetPolicy::class,
        LieuLeaveRequest::class => LieuLeavePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
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

            return ($project->isFocalPerson($user->id) || $project->isTeamLead($user->id) || $user->employee?->employee_code == 62) && $checkCurrentActivePeriod;
        });
    }
}
