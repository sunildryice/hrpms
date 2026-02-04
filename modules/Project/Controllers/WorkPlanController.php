<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Modules\Project\Requests\WorkPlan\StoreRequest as WorkPlanStoreRequest;
use Carbon\Carbon;
use Modules\Project\Models\Enums\WorkPlanStatus;
use Modules\Project\Models\WorkPlan;
use Modules\Project\Models\WorkPlanDetail;

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
        $weeks = [];
        $date = Carbon::now()->startOfYear();
        $limitDate = Carbon::now()->addMonth();
        $weekCount = 1;

        while ($date <= $limitDate) {
            $weekStart = $date->copy();
            $endOfCurrentYear = $date->copy()->endOfYear();

            // Calculate end of week (Saturday)
            // Carbon dayOfWeek: 0 (Sunday) - 6 (Saturday)
            $daysToSaturday = 6 - $weekStart->dayOfWeek;
            $weekEnd = $weekStart->copy()->addDays($daysToSaturday);

            // Cap at end of current year
            if ($weekEnd > $endOfCurrentYear) {
                $weekEnd = $endOfCurrentYear;
            }

            // Check duration (inclusive)
            $duration = $weekStart->diffInDays($weekEnd) + 1;

            // user requested to skip weeks starting with Thursday or having 2-3 days
            // If week starts on Thursday (and ends Saturday), it has 3 days.
            // If week has <= 3 days, we skip it.
            if ($duration > 3) {
                $now = Carbon::now()->startOfDay();
                $isCurrentWeek = $now->between($weekStart, $weekEnd);
                $isPastWeek = $weekEnd->lt($now);

                $weeks[] = [
                    'label' => 'Week ' . $weekCount++,
                    'start_date' => $weekStart->format('M j, Y'),
                    'end_date' => $weekEnd->format('M j, Y'),
                    'start_date_raw' => $weekStart->format('Y-m-d'),
                    'end_date_raw' => $weekEnd->format('Y-m-d'),
                    'is_current' => $isCurrentWeek,
                    'is_past' => $isPastWeek,
                ];
            }

            $date = $weekEnd->copy()->addDay();
        }

        return view('Project::WorkPlan.index', compact('weeks'));
    }

    public function details(Request $request, $startOfWeek, $endOfWeek)
    {
        $startDate = Carbon::parse($startOfWeek);
        $endDate = Carbon::parse($endOfWeek);
        $isEditable = WorkPlan::isEditable($startDate);
        $isStatusUpdatable = WorkPlan::isStatusUpdatable($startDate, $endDate);

        if ($request->ajax()) {
            $user = auth()->user();
            if (!$user->employee) {
                return DataTables::of(collect([]))->make(true);
            }

            $workPlan = $this->workPlans->findByDateAndEmployee($startDate->format('Y-m-d'), $user->employee->id);
            $query = $workPlan ? $workPlan->details()->with(['project', 'activity']) : collect([]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return $row->status ? ucfirst(str_replace('_', ' ', $row->status)) : 'Not Started';
                })
                ->addColumn('reason', function ($row) {
                    return $row->reason ?? '';
                })
                ->editColumn('status', function ($row) use ($isStatusUpdatable) {
                    $statusEnum = WorkPlanStatus::tryFrom($row->status) ?? WorkPlanStatus::NotStarted;


                    if (!$isStatusUpdatable || in_array($statusEnum, [WorkPlanStatus::Completed, WorkPlanStatus::NoRequired])) {
                        return '<span class="' . $statusEnum->colorClass() . '">' . $statusEnum->label() . '</span>';
                    }

                    $selectInput = '<select class="form-select form-select-sm work-plan-status" data-id="' . $row->id . '">';
                    foreach (WorkPlanStatus::cases() as $status) {
                        $selected = $row->status === $status->value ? 'selected' : '';
                        $selectInput .= '<option value="' . $status->value . '" ' . $selected . '>' . $status->label() . '</option>';
                    }
                    $selectInput .= '</select>';
                    return $selectInput;
                })
                ->addColumn('action', function ($row) use ($isEditable) {
                    if (!$isEditable) return '';

                    $btn = '';

                    $btn .= '<a href="' . route('work-plan.edit', $row->id) . '" class="btn btn-sm btn-outline-primary edit-work-plan" data-id="' . $row->id . '">
                    <i class="bi bi-pencil-square"></i></a>';
                    $btn .= ' <button class="btn btn-sm btn-outline-danger delete-work-plan" data-href="' . route('work-plan.destroy', $row->id) . '">
                    <i class="bi bi-trash "></i></button>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $projects = $this->projects->getAssignedProjects(auth()->user());

        return view('Project::WorkPlan.Detail.index', [
            'week' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'projects' => $projects,
            'isEditable' => $isEditable,
        ]);
    }

    public function getActivities(Request $request)
    {
        $projectId = $request->project_id;
        $activities = $this->projectActivities->model
            ->where('project_id', $projectId)
            ->whereIn('activity_level', ['activity', 'sub_activity'])
            ->get(['id', 'title']);

        return response()->json(['activities' => $activities]);
    }

    public function create(Request $request)
    {
        $week = [
            'start_date' => Carbon::parse($request->from_date),
            'end_date' => Carbon::parse($request->to_date),
        ];

        $projects = $this->projects->getAssignedProjects(auth()->user());

        return view('Project::WorkPlan.Detail.create', compact('week', 'projects'));
    }

    public function edit($id)
    {
        $workPlanDetail = \Modules\Project\Models\WorkPlanDetail::with(['workPlan', 'project'])->findOrFail($id);
        $week = [
            'start_date' => Carbon::parse($workPlanDetail->workPlan->from_date),
            'end_date' => Carbon::parse($workPlanDetail->workPlan->to_date),
        ];
        $projects = $this->projects->getAssignedProjects(auth()->user());

        // Eager load activities for the selected project to populate the dropdown
        $workPlanDetail->project->load('activities');

        return view('Project::WorkPlan.Detail.edit', compact('workPlanDetail', 'week', 'projects'));
    }

    public function update(WorkPlanStoreRequest $request, $id)
    {
        $data = $request->validated();
        $detail = \Modules\Project\Models\WorkPlanDetail::with('workPlan')->findOrFail($id);

        if (!\Modules\Project\Models\WorkPlan::isEditable($detail->workPlan->from_date)) {
            return response()->json(['message' => 'This work plan cannot be edited.'], 403);
        }

        $detail->update([
            'project_id' => $data['project_id'],
            'project_activity_id' => $data['activity_id'],
            'plan_tasks' => $data['planned_task'],
            // Add reason if added to DB
        ]);

        return response()->json(['message' => 'Work plan updated successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
        ]);

        if ($request->status === WorkPlanStatus::Completed->value && empty($request->reason)) {
            return response()->json(['message' => 'Reason is required when marking as Completed.'], 422);
        }

        $detail = WorkPlanDetail::with('workPlan')->findOrFail($id);

        if (!WorkPlan::isStatusUpdatable($detail->workPlan->from_date, $detail->workPlan->to_date)) {
            return response()->json(['message' => 'Status cannot be updated at this time.'], 403);
        }

        $detail->update([
            'status' => $request->status,
            'reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $detail = \Modules\Project\Models\WorkPlanDetail::with('workPlan')->findOrFail($id);

        if (!\Modules\Project\Models\WorkPlan::isEditable($detail->workPlan->from_date)) {
            return response()->json(['message' => 'This work plan cannot be deleted.'], 403);
        }

        $detail->delete();
        return response()->json(['message' => 'Work plan deleted successfully.']);
    }

    public function store(WorkPlanStoreRequest $request)
    {
        $data = $request->validated();

        if (!\Modules\Project\Models\WorkPlan::isEditable($data['from_date'])) {
            return response()->json(['message' => 'Work plan cannot be added for this week.'], 403);
        }

        $user = auth()->user();

        if (!$user->employee) {
            return response()->json(['message' => 'Employee record not found for user.'], 403);
        }

        // Find or Create WorkPlan
        $workPlan = $this->workPlans->findByDateAndEmployee($data['from_date'], $user->employee->id);

        if (!$workPlan) {
            $workPlan = $this->workPlans->createWorkPlan([
                'employee_id' => $user->employee->id,
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
            ]);
        }

        // Add Detail
        $this->workPlans->createWorkPlanDetail($workPlan->id, $data);

        return response()->json(['message' => 'Work plan added successfully.']);
    }
}
