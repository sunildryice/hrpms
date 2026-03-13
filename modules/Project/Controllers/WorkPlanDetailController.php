<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Project\Models\Enums\WorkPlanStatus;
use Modules\Project\Models\WorkPlan;
use Modules\Project\Models\WorkPlanDetail;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\WorkPlanDetailRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Modules\Project\Requests\WorkPlan\StoreRequest as WorkPlanStoreRequest;
use Yajra\DataTables\Facades\DataTables;

class WorkPlanDetailController extends Controller
{

    public function __construct(
        protected ProjectRepository         $projects,
        protected ProjectActivityRepository $projectActivities,
        protected WorkPlanRepository        $workPlans,
        protected WorkPlanDetailRepository  $workPlanDetails
    )
    {
    }

    public function index(Request $request, WorkPlan $workPlan)
    {
        $authUser = auth()->user();
        $isEditable = $authUser->can('update', $workPlan);
        $isStatusUpdatable = $authUser->can('updateStatus', $workPlan);

        if ($request->ajax()) {
            $query = $this->workPlanDetails->with('members')
                ->where('work_plan_id',$workPlan->id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($row) {
                    return $row->project?->short_name ?: ($row->project?->title ?? '-');
                })
                ->addColumn('work_plan_date', function ($row) {

                    $formattedDate = $row->work_plan_date ? Carbon::parse($row->work_plan_date)->format('M d, Y') : '-';
                    return $formattedDate;
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
                        $badges .= '<span class="badge bg-primary me-1 mb-1">' . e($memberName) . '</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($detailRow) use ($isEditable) {
                    if (!$isEditable) return '';
                    $btn = '';

                    $btn .= '<a href="' . route('work-plan.edit', $detailRow->id) . '" class="btn btn-sm btn-outline-primary edit-work-plan" data-id="' . $detailRow->id . '">
                    <i class="bi bi-pencil-square"></i></a>';
                    $btn .= ' <button class="btn btn-sm btn-outline-danger delete-work-plan" data-href="' . route('work-plan.destroy', $detailRow->id) . '">
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
            'workPlan' => $workPlan,
            'projects' => $projects,
            'isEditable' => $isEditable,
        ]);
    }

    public function getActivities(Request $request)
    {
        $projectId = $request->project_id;
        $activities = $this->projectActivities->select(['*'])
            ->where('project_id', $projectId)
            ->whereIn('activity_level', ['activity', 'sub_activity'])
            ->get(['id', 'title']);

        return response()->json(['activities' => $activities]);
    }

    public function create(Request $request, WorkPlan $workPlan)
    {
        $week = [
            'start_date' => $workPlan->from_date,
            'end_date' => $workPlan->to_date,
        ];
        $projects = $this->projects->getAssignedProjects(auth()->user());

        return view('Project::WorkPlan.Detail.create')
            ->withProjects($projects)
            ->withWeek($week)
            ->withWorkPlan($workPlan);
    }

    public function store(WorkPlanStoreRequest $request, WorkPlan $workPlan)
    {
        $data = $request->validated();
        $user = auth()->user();

        if (!isset($data['entries']) || !is_array($data['entries'])) {
            return response()->json(['message' => 'No entries provided.'], 422);
        }


        // determine overall plan range from the entry dates (week bounds already validated)
        $dates = collect($data['entries'])
            ->pluck('work_plan_date')
            ->filter()
            ->sort();

        // if ($dates->isEmpty()) {
        //     return response()->json(['message' => 'Entry dates are required.'], 422);
        // }


        $data['from_date'] = $workPlan->from_date;
        $data['to_date'] = $workPlan->to_date;


        // Create a temporary instance to check policy against the date
        $checkPlan = new WorkPlan(['from_date' => $data['from_date']]);
        if ($user->cannot('update', $checkPlan)) {
            return response()->json(['message' => 'Work plan cannot be added for this date.'], 403);
        }
        if (!$user->employee) {
            return response()->json(['message' => 'Employee record not found for user.'], 403);
        }

        $workPlan = $this->workPlans->findOrCreateWorkPlan(
            $user->employee->id,
            $data['from_date'],
            $data['to_date']
        );


        // Save each entry with its members and date
        foreach ($data['entries'] as $entry) {
            $entryData = [
                // 'work_plan_date' => $entry['work_plan_date'] ?? null,
                'project_id' => $entry['project_id'] ?? null,
                // 'activity_id' => $entry['activity_id'] ?? null,
                'planned_task' => $entry['planned_task'] ?? null,
                'members' => $entry['members'] ?? [],
            ];
            $this->workPlans->createWorkPlanDetail($workPlan->id, $entryData);
        }


        return redirect()->route('work-plan.details', $workPlan->id)
            ->with('success', 'Work plan added successfully.');
    }

    public function edit($id)
    {
        // dd($id);
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
        // $data['work_plan_date'] = $request->input('work_plan_date');
        $detail = $this->workPlans->findDetailById($id);


        // if (auth()->user()->cannot('update', $detail->workPlan)) {
        //     return response()->json(['message' => 'This work plan cannot be edited.'], 403);
        // }

        $this->workPlans->updateDetail($id, [
            // 'work_plan_date' => $data['work_plan_date'],
            'project_id' => $data['project_id'],
            // 'activity_id' => $data['activity_id'],
            'planned_task' => $data['planned_task'],
            'members' => $data['members'],
        ]);

        return response()->json(['message' => 'Work plan updated successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_map(fn($status) => $status->value, WorkPlanStatus::cases()))],
            'reason' => ['nullable', 'string'],
        ]);

        $statusEnum = WorkPlanStatus::tryFrom($data['status']) ?? WorkPlanStatus::NotStarted;
        $reason = trim((string)($data['reason'] ?? ''));

        if (in_array($statusEnum, [WorkPlanStatus::NoRequired]) && blank($reason)) {
            return response()->json(['message' => 'Reason is required.'], 422);
        }

        $detail = $this->workPlans->findDetailById($id);

        if (auth()->user()->cannot('updateStatus', $detail->workPlan)) {
            return response()->json(['message' => 'Status cannot be updated at this time.'], 403);
        }

        try {
            DB::beginTransaction();

            $this->workPlans->updateDetail($id, [
                'status' => $data['status'],
                'reason' => $reason ?: null,
            ]);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            report($exception);

            return response()->json([
                'message' => 'Unable to update status right now. Please try again.',
            ], 500);
        }

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
