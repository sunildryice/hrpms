<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Carbon\Carbon;

class EmployeeWorkPlanController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
        protected WorkPlanRepository $workPlans,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $currentWeekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);

            $query = $this->workPlans
                ->with(['projects', 'employee'])
                ->where('from_date', '>=', $currentWeekStart->format('Y-m-d'))
                ->where('to_date', '<=', $currentWeekEnd->format('Y-m-d'))
                ->select('work_plan.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_full_name', function ($row) {
                    return $row->employee->full_name ?? 'N/A';
                })
                ->addColumn('projects', function ($row) {
                    $projects = $row->projects->unique('id');
                    $badges = '';
                    foreach ($projects as $project) {
                        $badges .= '<span class="badge bg-secondary me-1">' . $project->short_name . '</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('work-plan.details', ['workPlan' => $row->id]) . '"
                                class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>';
                })
                ->rawColumns(['projects', 'action'])
                ->make(true);
        }

        return view('Project::EmployeeWorkPlan.index');
    }
}
