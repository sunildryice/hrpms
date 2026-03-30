<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Memo\Repositories\MemoRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\TravelRequest\Repositories\TravelReportRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\EmployeeRequest\Repositories\EmployeeRequestRepository;
use Modules\EventCompletion\Repositories\EventCompletionRepository;
use Modules\EmployeeExit\Repositories\EmployeeExitPayableRepository;
use Modules\AssetDisposition\Repositories\DispositionRequestRepository;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;

class SideBarComposer
{
    /**
     * Create a new header composer.
     *
     * @return void
     */
    public function __construct(
        protected ExitInterviewRepository       $exitInterview,
        protected ExitHandOverNoteRepository    $exitHandOverNote,
        protected AttendanceRepository          $attendances,
        protected FundRequestRepository         $fundRequests,
        protected EmployeeRequestRepository     $employeeRequests,
        protected EmployeeExitPayableRepository $employeeExitPayables,
        protected GoodRequestRepository         $goodRequests,
        protected GoodRequestAssetRepository    $goodRequestAssets,
        protected LeaveRequestRepository        $leaveRequests,
        protected LocalTravelRepository         $localTravels,
        protected MaintenanceRequestRepository  $maintenances,
        protected MemoRepository                $memos,
        protected PerformanceReviewRepository   $performanceReviews,
        protected TravelClaimRepository         $travelClaims,
        protected TravelRequestRepository       $travelRequests,
        protected TravelReportRepository        $travelReports,
        protected UserRepository                $users,
        protected VehicleRequestRepository      $vehicleRequests,
        protected WorkPlanRepository            $workPlans,
        protected EventCompletionRepository     $eventCompletion,
        protected LeaveEncashRepository         $leaveEncash,
        protected DispositionRequestRepository  $dispositionRequests,
        protected TravelAuthorizationRepository $travelAuthorization,
        protected WorkFromHomeRepository        $workFromHomes,
        protected OffDayWorkRepository          $offDayWorks,
        protected LieuLeaveRequestRepository    $lieuLeaveRequests,
        protected TimeSheetRepository           $timeSheets,
    )
    {
    }

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $authUser = auth()->user();
        $approveAssetHandoverCount = $this->goodRequestAssets->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('handover_status_id', config('constant.SUBMITTED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('handover_status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();

        $approveDispositionRequestCount = $this->dispositionRequests->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
            })->count();

        $approveGoodRequestCount = $this->goodRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id)
                        ->where('status_id', config('constant.VERIFIED_STATUS'))
                        ->whereNotNull('reviewer_id');
                });
                $q->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id)
                        ->where('status_id', config('constant.SUBMITTED_STATUS'))
                        ->whereNull('reviewer_id');
                });
            })->count();

        $receiveGoodRequestCount = $this->goodRequests->select(['id'])
            ->where(function ($q) {
                $q->where('is_direct_assign', '=', '1');
                $q->orWhere('is_direct_dispatch', '=', '1');
            })
            ->where('receiver_id', '=', $authUser->id)
            ->whereIn('status_id', [config('constant.ASSIGNED_STATUS'), config('constant.APPROVED_STATUS')])
            ->whereNull('received_at')
            ->count();

        $assignGoodRequestCount = $this->goodRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('logistic_officer_id', $authUser->id);
                $q->where('status_id', config('constant.APPROVED_STATUS'));
            })->count();

        $reviewLeaveCount = $this->leaveRequests->select(['id'])
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->count();

        $approveLeaveCount = $this->leaveRequests->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $hrApproveLeaveCount = $this->leaveRequests->getHrApproveLeaveRequests()->count();

        $reviewLeaveEncashCount = $this->leaveEncash->select(['id'])
            ->where('reviewer_id', $authUser->id)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->count();

        $approveLeaveEncashCount = $this->leaveEncash->select(['*'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approveLocalTravelCount = $this->localTravels->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approveTravelCount = $this->travelRequests->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();
        $approveTravelCancelCount = $this->travelRequests->select('id')
            ->where('approver_id', $authUser->id)
            ->where('status_id', config('constant.INIT_CANCEL_STATUS'))
            ->count();
        $approveTravelClaimCount = $this->travelClaims->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('recommender_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED2_STATUS'));
            })->count();
        $approveTravelReportCount = $this->travelReports->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $approveVehicleRequestCount = $this->vehicleRequests->select(['id'])
            ->where('vehicle_request_type_id', 2)
            ->where(function ($query) use ($authUser) {
                $query->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });
            })->count();


        $assignVehicleRequestCount = $this->vehicleRequests->select(['id'])
            ->where('vehicle_request_type_id', 1)
            ->where(function ($query) use ($authUser) {
                $query->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });
            })->count();

        $reviewGoodRequestCount = $this->goodRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $reviewTravelClaimCount = $this->travelClaims->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();

        $verifyEmployeeRequisitionCount = $this->employeeRequests
            ->where('reviewer_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->count();

        $approveEmployeeRequisitionCount = $this->employeeRequests
            ->where('approver_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.VERIFIED_STATUS'))
            ->count();

        $verifyAttendanceCount = $this->attendances
            ->where('reviewer_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->count();
        $approveAttendanceCount = $this->attendances
            ->where('approver_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.VERIFIED_STATUS'))
            ->count();

        $verifyMemoCount = $this->memos
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->whereHas('memoThrough', function ($q) use ($authUser) {
                $q->where('user_id', '=', $authUser->id);
            })
            ->count();

        $approveMemoCount = $this->memos->select(['id'])
            ->where(function ($query) use ($authUser) {
                $query->where('status_id', config('constant.RECOMMENDED_STATUS'))
                    ->whereHas('to', function ($q) use ($authUser) {
                        $q->where('user_id', $authUser->id);
                    });
            })->orWhere(function ($query) use ($authUser) {
                $query->where('status_id', config('constant.SUBMITTED_STATUS'))
                    ->where(function ($subQuery) use ($authUser) {
                        $subQuery->whereHas('memoThrough', function ($q) use ($authUser) {
                            $q->where('user_id', $authUser->id);
                        });
                        $subQuery->orWhere(function ($q) use ($authUser) {
                            $q->whereDoesntHave('memoThrough');
                            $q->whereHas('to', function ($qq) use ($authUser) {
                                $qq->where('user_id', $authUser->id);
                            });
                        });
                    });
            })->count();

        $verifyMaintenanceCount = $this->maintenances
            ->where('reviewer_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->count();
        $approveMaintenanceCount = $this->maintenances
            ->where('approver_id', '=', $authUser->id)
            ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.VERIFIED_STATUS')])
            ->count();

        $reviewEmployeeExitPayableCount = $this->employeeExitPayables
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])
            ->where('reviewer_id', $authUser->id)
            ->count();
        $approveEmployeeExitPayableCount = $this->employeeExitPayables
            ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', $authUser->id)
            ->count();

        $reviewPerCount = $this->performanceReviews->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('reviewer_id', '=', $authUser->id)
            ->count();

        $recommendPerCount = $this->performanceReviews->where('status_id', '=', config('constant.VERIFIED_STATUS'))
            ->where('recommender_id', '=', $authUser->id)
            ->count();

        $approvePerCount = $this->performanceReviews->where('status_id', '=', config('constant.RECOMMENDED_STATUS'))
            ->where('approver_id', '=', $authUser->id)
            ->count();

        $approveECRCount = $this->eventCompletion->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approveTACount = $this->travelAuthorization->select('id')
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])
            ->count();
        $reviewLeaveCount = $this->leaveRequests->select(['id'])
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->where('approver_id', $authUser->id)
            ->count();

        $approveExitInterviewCount = $this->exitInterview->with(['employee', 'status'])->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();
        $approveExitHandoverNoteCount = $this->exitHandOverNote->with(['employee', 'status'])->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();


        $approveWorkFromHomeRequestCount = $this->workFromHomes
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('approver_id', '=', auth()->id())
            ->count();

        $approveOffDayWorkCount = $this->offDayWorks
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('approver_id', '=', auth()->id())
            ->count();

        $approveLieuLeaveRequestCount = $this->lieuLeaveRequests
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('approver_id', '=', auth()->id())
            ->count();


        $approveMonthlyTimeSheetCount = $this->timeSheets
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('approver_id', '=', auth()->id())
            ->count();

        $view->withUser($authUser)
            ->withApproveAssetHandoverCount($approveAssetHandoverCount)
            ->withApproveExitInterviewCount($approveExitInterviewCount)
            ->withApproveAttendanceCount($approveAttendanceCount)
            ->withApproveEmployeeRequisitionCount($approveEmployeeRequisitionCount)
            ->withApproveEmployeeExitPayableCount($approveEmployeeExitPayableCount)
            ->withApproveExitHandoverNoteCount($approveExitHandoverNoteCount)
            ->withApproveGoodRequestCount($approveGoodRequestCount)
            ->withReviewLeaveCount($reviewLeaveCount)
            ->withApproveLeaveCount($approveLeaveCount)
            ->withApproveMaintenanceCount($approveMaintenanceCount)
            ->withApproveMemoCount($approveMemoCount)
            ->withApprovePerCount($approvePerCount)
            ->withReviewLeaveCount($reviewLeaveCount)
            ->withApproveLocalTravelCount($approveLocalTravelCount)
            ->withApproveTravelCount($approveTravelCount)
            ->withApproveTravelClaimCount($approveTravelClaimCount)
            ->withApproveTravelReportCount($approveTravelReportCount)
            ->withApproveVehicleRequestCount($approveVehicleRequestCount)
            ->withAssignGoodRequestCount($assignGoodRequestCount)
            ->withAssignVehicleRequestCount($assignVehicleRequestCount)
            ->withReviewEmployeeExitPayableCount($reviewEmployeeExitPayableCount)
            ->withReviewGoodRequestCount($reviewGoodRequestCount)
            ->withReceiveGoodRequestCount($receiveGoodRequestCount)
            ->withReviewTravelClaimCount($reviewTravelClaimCount)
            ->withReviewPerCount($reviewPerCount)
            ->withRecommendPerCount($recommendPerCount)
            ->withVerifyAttendanceCount($verifyAttendanceCount)
            ->withVerifyEmployeeRequisitionCount($verifyEmployeeRequisitionCount)
            ->withVerifyMaintenanceCount($verifyMaintenanceCount)
            ->withVerifyMemoCount($verifyMemoCount)
            ->withApproveECRCount($approveECRCount)
            ->withApproveLeaveEncashCount($approveLeaveEncashCount)
            ->withReviewLeaveEncashCount($reviewLeaveEncashCount)
            ->withApproveDispositionRequestCount($approveDispositionRequestCount)
            ->withApproveTravelCancelCount($approveTravelCancelCount)
            ->withApproveTACount($approveTACount)
            ->withHrApproveLeaveCount($hrApproveLeaveCount)
            ->withApproveWorkFromHomeRequestCount($approveWorkFromHomeRequestCount)
            ->withApproveOffDayWorkCount($approveOffDayWorkCount)
            ->withApproveLieuLeaveRequestCount($approveLieuLeaveRequestCount)
            ->withApproveMonthlyTimeSheetCount($approveMonthlyTimeSheetCount);

        return $view;
    }
}
