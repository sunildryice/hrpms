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
        $workPlans = $this->workPlans
            ->where('employee_id', '=', auth()->user()->employee->id ?? null)
            ->whereYear('from_date', Carbon::now()->year)
            ->whereYear('to_date', Carbon::now()->year)
            ->orderByRaw("
                CASE 
                    WHEN ? BETWEEN from_date AND to_date THEN 0   
                    WHEN to_date >= ? THEN 1                      
                    ELSE 2                                        
                END
            ", [$now, $now])
            ->orderBy('from_date', 'desc')
            ->get();

        return view('Project::WorkPlan.index', compact('workPlans', 'now'));
    }
}
