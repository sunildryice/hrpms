<?php

namespace Modules\LieuLeave\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\LieuLeave\Models\LieuLeaveBalance;

class LieuLeaveBalanceRepository extends Repository
{

    public function __construct(
        protected LieuLeaveBalance $lieuLeaveBalance,
        protected LieuLeaveRequestRepository $lieuLeaveRequest,
    ) {
        $this->model = $lieuLeaveBalance;
    }

    public function getAvailableLeaveForUse(int $userId, $date)
    {

        return $this->model->where('user_id', $userId)
            ->whereNull('lieu_leave_request_id')
            ->where('expires_at', '>', $date)
            ->orderBy('expires_at')
            ->get();
    }

    public function addBalance($userId, $offDayWorkId)
    {

        $earnedDate = Carbon::now();
        $expiresAt  = $earnedDate->copy()->addDays(30);
        $earnedMonth = $earnedDate->copy()->startOfMonth();

        $this->model->create([
            'user_id'            => $userId,
            'earned_date'       => $earnedDate->toDateString(),
            'earned_month'      => $earnedMonth->toDateString(),
            'off_day_work_id'   => $offDayWorkId,
            'expires_at'        => $expiresAt->toDateString(),
        ]);
    }


    public function countAppliedLeave(int $userId, Carbon $month): int
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        return $this->lieuLeaveRequest
            ->where('requester_id', '=', $userId)
            ->whereIn('status_id', [
                config('constant.APPROVED_STATUS'),
                config('constant.SUBMITTED_STATUS'),
            ])
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->count();
    }


    public function countLieuLeaveBalances(int $userId, $expiryDate): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('expires_at', '>', $expiryDate->toDateString())
            ->whereNull('lieu_leave_request_id')
            ->count();
    }

    public function getOffDayWorkAvailableDates(int $userId, $previousMonthDate)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('earned_date', '>=', $previousMonthDate->toDateString())
            ->whereNull('lieu_leave_request_id')
            ->pluck('earned_date', 'id');
    }
}
