<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;


class CalenderController extends Controller
{
    public function __construct(
        protected LeaveRequestRepository $leaveRequest,
        protected TravelRequestRepository $travelRequest,
        protected OffDayWorkRepository $offDayWork,
        protected LieuLeaveRequestRepository $lieuLeaveRequest,
        protected WorkFromHomeRepository $workFromHome,
        protected OfficeRepository $officeRepository,
    ) {}

    public function index($officeId, $month, $year)
    {

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth   = (clone $startOfMonth)->endOfMonth()->endOfDay();


        $leaveRequests = $this->leaveRequest->query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            });

        $sickLeaves = (clone $leaveRequests)->where('leave_type_id', config('constant.SICK_LEAVE'))->with(['requester'])->get();
        $annualLeaves = (clone $leaveRequests)->where('leave_type_id', config('constant.ANNUAL_LEAVE'))->with(['requester'])->get();

        $lieuLeaveRequests = $this->lieuLeaveRequest->query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            })
            ->with(['requester'])
            ->get();
        $workFromHomeRequests = $this->workFromHome->query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            })
            ->with(['requester'])
            ->get();

        $travelRequests = $this->travelRequest->query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('departure_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('return_date', [$startOfMonth, $endOfMonth]);
            })
            ->with(['requester'])
            ->get();


        $offDayWorks = $this->offDayWork->query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['requester'])
            ->get();

        $otherLeaves = $leaveRequests->whereNotIn('leave_type_id', [
            config('constant.SICK_LEAVE'),
            config('constant.ANNUAL_LEAVE'),
        ])->with(['requester'])->get();


        $office = $this->officeRepository->find($officeId);


        return response()->json([
            'sickLeaves' => $sickLeaves,
            'annualLeaves' => $annualLeaves,
            'lieuLeaveRequests' => $lieuLeaveRequests,
            'workFromHomeRequests' => $workFromHomeRequests,
            'travelRequests' => $travelRequests,
            'offDayWorks' => $offDayWorks,
            'otherLeaves' => $otherLeaves,
            'office' => $office,
        ]);
    }
}
