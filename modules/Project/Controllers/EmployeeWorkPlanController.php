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
        if ($request->has('week_start')) {
            $currentWeekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::SUNDAY);
        } else {
            $currentWeekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        }

        $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
        if ($request->ajax()) {


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

        $weeks = [];
        $existingWeeks = $this->workPlans
            ->select(['from_date', 'to_date'])
            ->whereNotNull('from_date')
            ->whereNotNull('to_date')
            ->distinct()
            ->orderBy('from_date', 'desc')
            ->get();

        foreach ($existingWeeks as $week) {
            if ($week->from_date && $week->to_date) {
                $label = $week->from_date->format('M j, Y') . ' - ' . $week->to_date->format('M j, Y');
                $weeks[$week->from_date->format('Y-m-d')] = $label;
            }
        }

        return view('Project::EmployeeWorkPlan.index', [
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
            'weeks' => $weeks,
        ]);
    }
}
