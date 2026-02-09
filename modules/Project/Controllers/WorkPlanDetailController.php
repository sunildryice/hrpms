<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Modules\Project\Requests\WorkPlan\StoreRequest as WorkPlanStoreRequest;
use Carbon\Carbon;
use Modules\Project\Models\Enums\WorkPlanStatus;
use Modules\Project\Models\WorkPlan;

class WorkPlanDetailController extends Controller
{
    public function __construct(
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
        protected WorkPlanRepository $workPlans,
    ) {}

    public function index(Request $request, WorkPlan $workPlan)
    {
        $isEditable = auth()->user()->can('update', $workPlan);
        $isStatusUpdatable = auth()->user()->can('updateStatus', $workPlan);

        if ($request->ajax()) {
            $user = auth()->user();
            if (!$user->employee) {
                return DataTables::of(collect([]))->make(true);
            }

            $query = $this->workPlans->getWorkPlanDetails($workPlan->id);

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
                ->addColumn('members', function ($row) {


                    $members = $row->members->pluck('full_name')->toArray();

                    $badges = '';

                    foreach ($members as &$memberName) {
                        $badges .= '<span class="badge bg-secondary me-1 mb-1">' . e($memberName) . '</span>';
                    }
                    return $badges;
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
                ->rawColumns(['action', 'status', 'members'])
                ->make(true);
        }

        $projects = $this->projects->getAssignedProjects(auth()->user());

        return view('Project::WorkPlan.Detail.index', [
            'week' => [
                'start_date' => $workPlan->from_date,
                'end_date' => $workPlan->to_date,
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

        // $assignedMembers = $projects->members;

        return view('Project::WorkPlan.Detail.create', compact('week', 'projects'));
    }

    public function store(WorkPlanStoreRequest $request)
    {
        $data = $request->validated();
        $data['members'] = $request->input('members', []);
        $user = auth()->user();

        // Create a temporary instance to check policy against the date
        $checkPlan = new WorkPlan(['from_date' => $data['from_date']]);

        if ($user->cannot('update', $checkPlan)) {
            return response()->json(['message' => 'Work plan cannot be added for this week.'], 403);
        }

        if (!$user->employee) {
            return response()->json(['message' => 'Employee record not found for user.'], 403);
        }

        $workPlan = $this->workPlans->findOrCreateWorkPlan(
            $user->employee->id,
            $data['from_date'],
            $data['to_date']
        );

        $this->workPlans->createWorkPlanDetail($workPlan->id, $data);

        return response()->json(['message' => 'Work plan added successfully.']);
    }

    public function edit($id)
    {
        $workPlanDetail = $this->workPlans->findDetailById($id);
        $week = [
            'start_date' => Carbon::parse($workPlanDetail->workPlan->from_date),
            'end_date' => Carbon::parse($workPlanDetail->workPlan->to_date),
        ];
        $projects = $this->projects->getAssignedProjects(auth()->user());

        // Eager load related data required for edit modal dropdowns
        $workPlanDetail->project->load([
            'activities.members',
            'members',
            'teamLead',
            'focalPerson',
        ]);

        return view('Project::WorkPlan.Detail.edit', compact('workPlanDetail', 'week', 'projects'));
    }

    public function update(WorkPlanStoreRequest $request, $id)
    {
        $data = $request->validated();
        $data['members'] = $request->input('members', []);
        $detail = $this->workPlans->findDetailById($id);

        if (auth()->user()->cannot('update', $detail->workPlan)) {
            return response()->json(['message' => 'This work plan cannot be edited.'], 403);
        }

        $this->workPlans->updateDetail($id, [
            'project_id' => $data['project_id'],
            'activity_id' => $data['activity_id'],
            'planned_task' => $data['planned_task'],
            'members' => $data['members'],
        ]);

        return response()->json(['message' => 'Work plan updated successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
        ]);

        if (in_array($request->status, [WorkPlanStatus::Completed->value, WorkPlanStatus::NoRequired->value]) && empty($request->reason)) {
            return response()->json(['message' => 'Reason is required.'], 422);
        }

        $detail = $this->workPlans->findDetailById($id);

        if (auth()->user()->cannot('updateStatus', $detail->workPlan)) {
            return response()->json(['message' => 'Status cannot be updated at this time.'], 403);
        }

        $this->workPlans->updateDetail($id, [
            'status' => $request->status,
            'reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $detail = $this->workPlans->findDetailById($id);

        if (auth()->user()->cannot('delete', $detail->workPlan)) {
            return response()->json(['message' => 'This work plan cannot be deleted.'], 403);
        }

        $this->workPlans->deleteDetail($id);
        return response()->json(['message' => 'Work plan deleted successfully.']);
    }
}
