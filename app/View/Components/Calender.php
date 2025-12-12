<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;

class Calender extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        protected LeaveRequestRepository $leaveRequest,
        protected TravelRequestRepository $travelRequest,
        protected OffDayWorkRepository $offDayWork,
        protected LieuLeaveRequestRepository $lieuLeaveRequest,
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $today = Carbon::now();

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth   = $today->copy()->endOfMonth();

        $leaveRequests = $this->leaveRequest->query()
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
            ->with(['requester'])
            ->get();

        $travelRequests = $this->travelRequest->query()
            ->whereBetween('departure_date', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('return_date', [$startOfMonth, $endOfMonth])
            ->with(['requester'])
            ->get();

        $offDayWorks = $this->offDayWork->query()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['requester'])
            ->get();

        $lieuLeaveRequest = $this->lieuLeaveRequest->query()
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
            ->with(['requester'])
            ->get();



        return view('components.calender', [
            'leaveRequests' => $leaveRequests,
            'travelRequests' => $travelRequests,
            'offDayWorks' => $offDayWorks,
            'lieuLeaveRequest' => $lieuLeaveRequest,
        ]);
    }
}
