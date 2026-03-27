<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\ViewUserTimeSheet;
use Modules\TravelRequest\Repositories\TravelRequestRepository;

class ViewUserTimeSheetRepository extends Repository
{
    public function __construct(
        ViewUserTimeSheet $model,
        protected UserRepository $user,
        protected OfficeRepository $office,
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
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getApproverTimeSheets($approverId)
    {
        return $this->model
            ->where('approver_id', $approverId)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getApprovedTimeSheets($userId)
    {
        return $this->model
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getAbsenceReason(int $employeeId, string $date): string
    {
        $carbonDate = Carbon::parse($date);

        $user = $this->user->find($employeeId);
        $officeId = $user->employee->latestTenure?->office->id;

        if ($this->leaveRequests->isEmployeeOnApprovedLeave($employeeId, $date)) {
            return '<span class="text-warning fw-bold">On Leave</span>';
        }

        if ($this->lieuLeaveRequests->isEmployeeOnApprovedLieuLeave($employeeId, $date)) {
            return '<span class="text-purple fw-bold">On Lieu Leave</span>';
        }

        if ($this->travelRequests->isEmployeeOnApprovedTravel($employeeId, $date)) {
            return '<span class="text-info fw-bold">On Travel</span>';
        }

        if ($officeId) {
            $holidays = $this->office->getOfficeHolidays($officeId);
            if (in_array($date, $holidays)) {
                return '<span class="text-success fw-bold">Holiday</span>';
            }
        }

        if ($carbonDate->isWeekend()) {
            return '<span class="text-danger fw-bold">Weekend</span>';
        }

        return 'No timesheet entries';
    }
}
