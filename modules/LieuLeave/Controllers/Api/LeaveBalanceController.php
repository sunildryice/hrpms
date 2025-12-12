<?php

namespace Modules\LieuLeave\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;

class LeaveBalanceController extends Controller
{
    public function __construct(
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
    ) {}


    public function index(Request $request, $month)
    {
        $month = Carbon::parse($month);
        $userId = auth()->id();

        $appliedLeaveofMonth = $this->lieuLeaveBalance->countAppliedLeave($userId, $month);
        $lieuLeaveBalance =  $this->lieuLeaveBalance->countLieuLeaveBalances($userId, $month);


        if ($appliedLeaveofMonth > 0 || $lieuLeaveBalance == 0) {
            $availableBalanceofMonthStatus = 'Not Available';
        } else {
            $availableBalanceofMonthStatus = 'Available';
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'available_balance_status' => $availableBalanceofMonthStatus,
            ],
        ]);
    }
}
