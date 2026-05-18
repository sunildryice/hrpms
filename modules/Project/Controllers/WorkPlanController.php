<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Carbon\Carbon;

class WorkPlanController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
        protected WorkPlanRepository $workPlans,
    ) {
    }

    public function index(Request $request)
    {
        $now = Carbon::now()->startOfDay();
        $employeeId = auth()->user()->employee->id ?? null;

        $workPlans = $this->workPlans
            ->where('employee_id', '=', $employeeId)
            ->whereYear('from_date', Carbon::now()->year)
            ->whereYear('to_date', Carbon::now()->year)
            ->orderByRaw("
            CASE 
                WHEN ? BETWEEN from_date AND to_date THEN 0      -- Current week
                WHEN from_date > ? THEN 1                        -- Future weeks
                ELSE 2                                           -- Past weeks
            END ASC
        ", [$now, $now])
            ->orderByRaw("
            CASE 
                WHEN ? BETWEEN from_date AND to_date THEN 0
                WHEN from_date > ? THEN from_date                -- Future: Soonest first (ASC)
                ELSE 9999999999 - UNIX_TIMESTAMP(from_date)      -- Past: Most recent first (reverse)
            END ASC
        ", [$now, $now])
            ->get();

        return view('Project::WorkPlan.index', compact('workPlans', 'now'));
    }
}
