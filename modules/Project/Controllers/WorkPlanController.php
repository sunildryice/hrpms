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
    ) {}

    public function index(Request $request)
    {
        $workPlans = $this->workPlans
            ->where('employee_id', '=', auth()->user()->employee->id ?? null)
            ->whereYear('from_date', Carbon::now()->year)
            ->whereYear('to_date', Carbon::now()->year)
            ->get();



        $now = Carbon::now()->startOfDay();

        return view('Project::WorkPlan.index', compact('workPlans', 'now'));
    }
}
