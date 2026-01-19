<?php

namespace Modules\LieuLeave\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;

class OffDayWorkController extends Controller
{
    public function __construct(
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
    ) {}


    public function index(Request $request, $date)
    {
        $leaveDate = Carbon::parse($date);

        $userId = auth()->id();

        $availableOffDayWorkDates = $this->lieuLeaveBalance->getOffDayWorkAvailableDates(
            $userId,
            $leaveDate->copy()->subMonth(),
        );


        return response()->json([
            'status' => 'success',
            'data' => [
                'available_off_day_work_dates' => $availableOffDayWorkDates,
            ],
        ]);
    }
}
