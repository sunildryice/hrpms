<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Leave;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LieuLeave\Models\LieuLeaveRequest;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\Project\Models\ViewUserTimeSheet;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\TravelRequest\Repositories\TravelRequestRepository;

class ViewUserTimeSheetRepository extends Repository
{
    public function __construct(
        ViewUserTimeSheet $model,
        protected LeaveRequestRepository $leaveRequests,
        protected TravelRequestRepository $travelRequests,
        protected LieuLeaveRequestRepository $lieuLeaveRequests
    ) {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getUserTimeSheets($userId)
    {
        return $this->model
            ->where('requester_id', $userId)
            ->orderBy('year', 'desc')
            ->get();
    }

    public function getApproverTimeSheets($approverId)
    {
        return $this->model
            ->where('approver_id', $approverId)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->orderBy('year', 'desc')
            ->get();
    }

    public function getApprovedTimeSheets($userId)
    {
        return $this->model
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->orderBy('year', 'desc')
            ->get();
    }

    public function getAbsenceReason(int $employeeId, string $date): string
    {
        $carbonDate = Carbon::parse($date);

        if ($this->leaveRequests->isEmployeeOnApprovedLeave($employeeId, $date)) {
            return '<span class="text-warning fw-bold">On Leave</span>';
        }

        if ($this->lieuLeaveRequests->isEmployeeOnApprovedLieuLeave($employeeId, $date)) {
            return '<span class="text-purple fw-bold">On Lieu Leave</span>';
        }

        if ($this->travelRequests->isEmployeeOnApprovedTravel($employeeId, $date)) {
            return '<span class="text-info fw-bold">On Travel</span>';
        }

        if ($carbonDate->isWeekend()) {
            return '<span class="text-danger fw-bold">Weekend</span>';
        }

        return 'No timesheet entries';
    }
}
