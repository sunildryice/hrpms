<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\AssetDisposition\Repositories\DispositionRequestRepository;
use Modules\DistributionRequest\Repositories\DistributionHandoverRepository;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\EmployeeExit\Repositories\EmployeeExitPayableRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\EmployeeRequest\Repositories\EmployeeRequestRepository;
use Modules\EventCompletion\Repositories\EventCompletionRepository;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\Memo\Repositories\MemoRepository;
use Modules\Mfr\Repositories\TransactionRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TransportationBill\Repositories\TransportationBillRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Repositories\TravelReportRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;

class SideBarComposer
{
    /**
     * Create a new header composer.
     *
     * @return void
     */
    public function __construct(
        protected AdvanceRequestRepository       $advanceRequests,
        protected ExitInterviewRepository        $exitInterview,
        protected ExitHandOverNoteRepository     $exitHandOverNote,
        protected AttendanceRepository           $attendances,
        protected DistributionHandoverRepository $distributionHandovers,
        protected DistributionRequestRepository  $distributionRequests,
        protected FundRequestRepository          $fundRequests,
        protected EmployeeRequestRepository      $employeeRequests,
        protected EmployeeExitPayableRepository  $employeeExitPayables,
        protected GoodRequestRepository          $goodRequests,
        protected GoodRequestAssetRepository     $goodRequestAssets,
        protected LeaveRequestRepository         $leaveRequests,
        protected LocalTravelRepository          $localTravels,
        protected MaintenanceRequestRepository   $maintenances,
        protected MemoRepository                 $memos,
        protected PaymentSheetRepository         $paymentSheets,
        protected PerformanceReviewRepository    $performanceReviews,
        protected PurchaseOrderRepository        $purchaseOrders,
        protected PurchaseRequestRepository      $purchaseRequests,
        protected SettlementRepository           $settlements,
        protected TrainingRequestRepository      $trainingRequests,
        protected TransportationBillRepository   $transportationBills,
        protected TravelClaimRepository          $travelClaims,
        protected TravelRequestRepository        $travelRequests,
        protected TravelReportRepository         $travelReports,
        protected UserRepository                 $users,
        protected VehicleRequestRepository       $vehicleRequests,
        protected WorkPlanRepository             $workPlans,
        protected EventCompletionRepository      $eventCompletion,
        protected LeaveEncashRepository          $leaveEncash,
        protected DispositionRequestRepository   $dispositionRequests,
        protected TravelAuthorizationRepository  $travelAuthorization,
        protected TransactionRepository          $transactions,
    ) {}

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $authUser = auth()->user();
        $approveAdvanceRequestCount = $this->advanceRequests->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();
        $approveAssetHandoverCount = $this->goodRequestAssets->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('handover_status_id', config('constant.SUBMITTED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('handover_status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();

        $approveDistributionHandoverCount = $this->distributionHandovers->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $receiveDistributionHandoverCount = $this->distributionHandovers->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('receiver_id', $authUser->id);
                $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.RECEIVED_STATUS')]);
            })->count();
        $approveDistributionRequestCount = $this->distributionRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();
        $approveFundRequestCount = $this->fundRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->whereIn('status_id', [config('constant.VERIFIED3_STATUS'), config('constant.RECOMMENDED_STATUS')]);
            })->count();
        $reviewFundRequestCount = $this->fundRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED2_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->whereNull('certifier_id');
                $q->whereNull('checker_id');
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $checkFundRequestCount = $this->fundRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('checker_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $certifyFundRequestCount = $this->fundRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('certifier_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED_STATUS'));
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

        $reviewLeaveEncashCount = $this->leaveEncash->select(['id'])
            ->where('reviewer_id', $authUser->id)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->count();

        $approveLeaveEncashCount = $this->leaveEncash->select(['*'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approvePaymentSheetCount = $this->paymentSheets->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approveRecommendedPaymentSheetCount = $this->paymentSheets->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED2_STATUS'));
            })->count();

        $approvePoCount = $this->purchaseOrders->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();
        $approvePrCount = $this->purchaseRequests->select(['id'])
            ->where('approver_id', $authUser->id)
            ->where('status_id', config('constant.VERIFIED_STATUS'))
            ->count();
        $approveRecommendedPrCount = $this->purchaseRequests->select(['id'])
            ->where('approver_id', $authUser->id)
            ->where('status_id', config('constant.RECOMMENDED2_STATUS'))
            ->count();
        $approveLocalTravelCount = $this->localTravels->select(['id'])
            ->where('approver_id', $authUser->id)
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->count();

        $approveSettlementCount = $this->settlements->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->whereIn('status_id', [config('constant.VERIFIED2_STATUS'), config('constant.RECOMMENDED_STATUS')]);
            })->count();

        $approveTrainingRequestCount = $this->trainingRequests->where('status_id', '=', config('constant.RECOMMENDED2_STATUS'))
            ->where('approver_id', '=', $authUser->id)
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

        $approveWayBillCount = $this->transportationBills->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->orWhere(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
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

        $verifyPrCount = $this->purchaseRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('budget_verifier_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();

        $reviewPrCount = $this->purchaseRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED2_STATUS'));
            })->count();

        $reviewRecommendedPrCount = $this->purchaseRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('verifier_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
            })->count();
        $reviewPoCount = $this->purchaseOrders->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $reviewSettlementCount = $this->settlements->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $reviewTrainingRequestCount = $this->trainingRequests->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->where('reviewer_id', '=', $authUser->id)
            ->count();
        $recommendTrainingRequestCount = $this->trainingRequests->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')])
            ->where('recommender_id', '=', $authUser->id)
            ->count();
        $verifySettlementCount = $this->settlements->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('verifier_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED_STATUS'));
            })->count();
        $reviewTravelClaimCount = $this->travelClaims->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();

        $verifyAdvanceRequestCount = $this->advanceRequests->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('verifier_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();
        $verifyPaymentSheetCount = $this->paymentSheets->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('verifier_id', $authUser->id);
                $q->where('status_id', config('constant.SUBMITTED_STATUS'));
            })->count();

        $verifyRecommendedPaymentSheetCount = $this->paymentSheets->select(['id'])
            ->where(function ($q) use ($authUser) {
                $q->where('reviewer_id', $authUser->id);
                $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
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

        $approveWorkPlanCount = $this->workPlans
            ->where('approver_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
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

        $reviewTransactionCount = $this->transactions->select('id')
            ->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])->count();

        $verifyTransactionCount = $this->transactions->select('id')
            ->whereIn('status_id', [config('constant.VERIFIED_STATUS')])->count();

        $recommendTransactionCount = $this->transactions->select('id')
            ->whereIn('status_id', [config('constant.VERIFIED2_STATUS')])->count();

        $approveTransactionCount = $this->transactions->select('id')
            ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS')])->count();

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

        $view->withUser($authUser)
            ->withApproveAdvanceRequestCount($approveAdvanceRequestCount)
            ->withApproveAssetHandoverCount($approveAssetHandoverCount)
            ->withApproveExitInterviewCount($approveExitInterviewCount)
            ->withApproveAttendanceCount($approveAttendanceCount)
            ->withApproveWorkPlanCount($approveWorkPlanCount)
            ->withApproveDistributionHandoverCount($approveDistributionHandoverCount)
            ->withReceiveDistributionHandoverCount($receiveDistributionHandoverCount)
            ->withApproveDistributionRequestCount($approveDistributionRequestCount)
            ->withApproveEmployeeRequisitionCount($approveEmployeeRequisitionCount)
            ->withApproveEmployeeExitPayableCount($approveEmployeeExitPayableCount)
            ->withApproveExitHandoverNoteCount($approveExitHandoverNoteCount)
            ->withApproveFundRequestCount($approveFundRequestCount)
            ->withCheckFundRequestCount($checkFundRequestCount)
            ->withCertifyFundRequestCount($certifyFundRequestCount)
            ->withApproveGoodRequestCount($approveGoodRequestCount)
            ->withReviewLeaveCount($reviewLeaveCount)
            ->withApproveLeaveCount($approveLeaveCount)
            ->withApproveMaintenanceCount($approveMaintenanceCount)
            ->withApproveMemoCount($approveMemoCount)
            ->withApprovePoCount($approvePoCount)
            ->withApprovePerCount($approvePerCount)
            ->withApprovePrCount($approvePrCount)
            ->withVerifyPrCount($verifyPrCount)
            ->withReviewLeaveCount($reviewLeaveCount)
            ->withApproveRecommendedPrCount($approveRecommendedPrCount)
            ->withApproveLocalTravelCount($approveLocalTravelCount)
            ->withApprovePaymentSheetCount($approvePaymentSheetCount)
            ->withApproveRecommendedPaymentSheetCount($approveRecommendedPaymentSheetCount)
            ->withApproveSettlementCount($approveSettlementCount)
            ->withApproveTrainingRequestCount($approveTrainingRequestCount)
            ->withApproveTravelCount($approveTravelCount)
            ->withApproveTravelClaimCount($approveTravelClaimCount)
            ->withApproveTravelReportCount($approveTravelReportCount)
            ->withApproveVehicleRequestCount($approveVehicleRequestCount)
            ->withApproveWayBillCount($approveWayBillCount)
            ->withAssignGoodRequestCount($assignGoodRequestCount)
            ->withAssignVehicleRequestCount($assignVehicleRequestCount)
            ->withReviewEmployeeExitPayableCount($reviewEmployeeExitPayableCount)
            ->withReviewFundRequestCount($reviewFundRequestCount)
            ->withReviewGoodRequestCount($reviewGoodRequestCount)
            ->withReceiveGoodRequestCount($receiveGoodRequestCount)
            ->withReviewPoCount($reviewPoCount)
            ->withReviewSettlementCount($reviewSettlementCount)
            ->withReviewTrainingRequestCount($reviewTrainingRequestCount)
            ->withRecommendTrainingRequestCount($recommendTrainingRequestCount)
            ->withReviewTravelClaimCount($reviewTravelClaimCount)
            ->withReviewPrCount($reviewPrCount)
            ->withReviewRecommendedPrCount($reviewRecommendedPrCount)
            ->withReviewPerCount($reviewPerCount)
            ->withRecommendPerCount($recommendPerCount)
            ->withVerifyAdvanceRequestCount($verifyAdvanceRequestCount)
            ->withVerifyAttendanceCount($verifyAttendanceCount)
            ->withVerifyPaymentSheetCount($verifyPaymentSheetCount)
            ->withReviewTransactionCount($reviewTransactionCount)
            ->withVerifyTransactionCount($verifyTransactionCount)
            ->withRecommendTransactionCount($recommendTransactionCount)
            ->withApproveTransactionCount($approveTransactionCount)
            ->withVerifyRecommendedPaymentSheetCount($verifyRecommendedPaymentSheetCount)
            ->withVerifyEmployeeRequisitionCount($verifyEmployeeRequisitionCount)
            ->withVerifyMaintenanceCount($verifyMaintenanceCount)
            ->withVerifyMemoCount($verifyMemoCount)
            ->withApproveECRCount($approveECRCount)
            ->withApproveLeaveEncashCount($approveLeaveEncashCount)
            ->withReviewLeaveEncashCount($reviewLeaveEncashCount)
            ->withApproveDispositionRequestCount($approveDispositionRequestCount)
            ->withApproveTravelCancelCount($approveTravelCancelCount)
            ->withApproveTACount($approveTACount)
            ->withVerifySettlementCount($verifySettlementCount);

        return $view;
    }
}
