<?php

namespace Modules\LieuLeave\Repositories;

use App\Repositories\Repository;
use Modules\LieuLeave\Models\LieuLeaveRequest;

class LieuLeaveRequestRepository extends Repository
{
    public function __construct(protected LieuLeaveRequest $lieuLeaveRequest)
    {
        $this->model = $lieuLeaveRequest;
    }

    public function getLieuLeaveRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'lieu_leave_request_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('lieu_leave_request_number') + 1;
        return $max;
    }

    public function getLieuLeaveRequestsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getEmployeesOnLieuLeave()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))->with(['requester'])
            ->get();
    }

    public function getUpcomingLieuLeave()
    {
        $now = date('Y-m-d');
        $futureDate = now()->addDays(7)->format('Y-m-d');

        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '>', $now)
            ->whereBetween('start_date', [$now, $futureDate])
            ->with(['requester'])
            ->get();
    }

    public function isEmployeeOnApprovedLieuLeave(int $employeeId, string $date): bool
    {
        return $this->model
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where('requester_id', $employeeId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }
}
