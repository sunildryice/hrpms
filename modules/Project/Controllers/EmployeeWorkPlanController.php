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
use Modules\Project\Models\Enums\WorkPlanStatus;

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
                ->join('employees', 'employees.id', '=', 'work_plan.employee_id')
                ->where('from_date', '>=', $currentWeekStart->format('Y-m-d'))
                ->where('to_date', '<=', $currentWeekEnd->format('Y-m-d'))
                ->select('work_plan.*')
                ->orderBy('employees.full_name', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_full_name', function ($row) {
                    return $row->employee->full_name ?? 'N/A';
                })
                ->filterColumn('employee_full_name', function ($query, $keyword) {
                    $query->where('employees.full_name', 'like', "%{$keyword}%");
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
                    return '<a href="' . route('employee-work-plan.details', ['workPlan' => $row->id]) . '"
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

    public function show(Request $request, $id)
    {
        $workPlanDetail = $this->workPlans->with(['employee', 'projects.activities', 'details'])->findOrFail($id);

        if ($request->ajax()) {
            $user = auth()->user();
            if (!$user->employee) {
                return DataTables::of(collect([]))->make(true);
            }

            $query = $this->workPlans->getWorkPlanDetails($id);




            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return $row->status ? ucfirst(str_replace('_', ' ', $row->status)) : 'Not Started';
                })
                ->addColumn('reason', function ($row) {
                    return $row->reason ?? '';
                })
                ->editColumn('status', function ($row) {
                    $statusEnum = WorkPlanStatus::tryFrom($row->status) ?? WorkPlanStatus::NotStarted;

                    return '<span class="' . $statusEnum->colorClass() . '">' . $statusEnum->label() . '</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $details = $workPlanDetail->details;
        $stats = [
            'total' => $details->count(),
            'completed' => $details->where('status', WorkPlanStatus::Completed->value)->count(),
            'partially_completed' => $details->where('status', WorkPlanStatus::PartiallyCompleted->value)->count(),
            'no_required' => $details->where('status', WorkPlanStatus::NoRequired->value)->count(),
            'not_started' => $details->filter(function ($detail) {
                return is_null($detail->status) || $detail->status === WorkPlanStatus::NotStarted->value;
            })->count(),
        ];


        $week = [
            'start_date' => $workPlanDetail->from_date,
            'end_date' => $workPlanDetail->to_date,
        ];

        return view('Project::EmployeeWorkPlan.show', [
            'workPlan' => $workPlanDetail,
            'week' => $week,
            'stats' => $stats,
        ]);
    }
}
