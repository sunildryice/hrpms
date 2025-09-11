<?php

namespace Modules\ExitStaffClearance\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\ExitStaffClearance\Repositories\PerformanceReviewKeyGoalRepository;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;

class PerformanceReviewKeyGoalController extends Controller
{
    public function __construct(
        protected StaffClearanceRepository $staffClearance,
        protected PerformanceReviewKeyGoalRepository $staffClearanceKeyGoal
    ) {
    }

    public function getKeyGoals($performanceId)
    {
        $staffClearance = $this->staffClearance->find($performanceId);

        return response()->json([
            'type' => 'success',
            'keyGoals' => $staffClearance->keyGoals()->where('type', 'current')->get()
        ], 200);
    }
}
