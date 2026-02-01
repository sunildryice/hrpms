<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ProjectActivityStatusLog;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Requests\ProjectActivity\StoreRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Requests\ProjectActivity\UpdateRequest;

class ProjectActivityController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    ) {
    }
    public function index(Request $request, Project $project)
    {
        $authUser = auth()->user();
        $data = $this->projectActivity
            ->where('project_id', '=', $project->id)
            ->when($project->isFocalPerson($authUser->id) || $project->isTeamLead($authUser->id), function ($query) {
                // Focal Person or Team Lead can see all activities
                return $query;
            }, function ($query) use ($authUser) {
                // Other users can see only assigned activities
                return $query->whereHas('members', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                });
            })
            ->orderBy('activity_stage_id')
            ->orderBy('parent_id');

        $authUser = auth()->user();
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('start_date', function ($row) {
                return $row->start_date?->format('M d, Y');
            })
            ->editColumn('completion_date', function ($row) {
                return $row->completion_date?->format('M d, Y');
            })
            ->addColumn('activity_stage', function ($row) {
                return $row->stage->title;
            })
            ->addColumn('parent', function ($row) {
                return $row->parent?->title;
            })
            ->addColumn('activity_level', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->activity_level));
            })
            ->editColumn('status', function ($row) {

                $selectInput = '';

                if ($this->checkStatusDisplay($row)) {
                    $selectInput .= '<select class="form-select form-select-sm activity-status-change" data-activity-id="' . $row->id . '">';
                    foreach (ActivityStatus::cases() as $status) {

                        $selected = $row->status === $status->value ? 'selected' : '';

                        $disabled = $this->checkSelectDisableStatus($row, $status);

                        $selectInput .= '<option value="' . $status->value . '" ' . $selected . ' ' . $disabled . '>' . ucfirst(str_replace('_', ' ', $status->value)) . '</option>';
                    }
                    $selectInput .= '</select>';
                } else {
                    $selectInput .= '<span class="' . ActivityStatus::from($row->status)->colorClass() . '">' . ActivityStatus::from($row->status)->label() . '</span>';
                }
                return $selectInput;
            })
            ->addColumn('action', function ($row) use ($authUser) {

                $btn = '';
                if ($row->activity_level != ActivityLevel::Theme->value) {
                    $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('project-activity.show', $row->id) . '" rel="tooltip" title="View Project Activity">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                }

                if (Gate::allows('manage-project-activity-on-certain-time', $row->project)) {
                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-project-activity-modal-form " href="';
                    $btn .= route('project-activity.edit', $row->id) . '" rel="tooltip" title="Edit Project Activity">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';


                    if ($row->children->isEmpty()) {
                        $btn .= ' <button class="btn btn-outline-danger btn-sm delete-project-activity delete-record"
                data-href="';
                        $btn .= route('project-activity.destroy', $row->id) . '"
                data-id="';
                        $btn .= $row->id . '" rel="tooltip" title="Delete Project Activity">';
                        $btn .= '<i class="bi bi-trash"></i></button>';
                    }
                }
                if ($row->activity_level !== ActivityLevel::Theme->value) {
                    // $isAssigned = $row->isUserAssignedToActivity($authUser->id, $row->id);
                    // if ($isAssigned) {
                    $btn .= ' <a class="btn btn-outline-info btn-sm open-timesheet-modal-form" href="' . route('project-activity.timesheet.create', $row->id) . '" rel="tooltip" title="Add Timesheet">';
                    $btn .= '<i class="bi bi-clock"></i></a>';
                    // }
                }
                $btn .= ' <a class="btn btn-outline-primary btn-sm open-project-activity-extension-modal-form" href="'
                    . route('project-activity.extension.create', $row->id)
                    . '" rel="tooltip" title="Request Extension">'
                    . '<i class="bi bi-calendar-plus"></i></a>';

                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function checkSelectDisableStatus($row, $status)
    {
        return ($status->value == ActivityStatus::Completed->value && $row->status != ActivityStatus::UnderProgress->value) ? 'disabled' : '';
    }

    public function checkStatusDisplay(ProjectActivity $projectActivity)
    {
        $authUser = auth()->user();

        $notIsNoRequired = $projectActivity->status != ActivityStatus::NoRequired->value;
        $notIsCompleted = $projectActivity->status != ActivityStatus::Completed->value;
        $isTheme = $projectActivity->activity_level == ActivityLevel::Theme->value;

        if (Gate::allows('manage-project-activity-on-certain-time', $projectActivity->project) && ($notIsNoRequired && $notIsCompleted && !$isTheme)) {
            return true;
        }

        return false;
    }

    public function create(Project $project)
    {
        $activityLevels = ActivityLevel::cases();
        $status = ActivityStatus::cases();
        $stages = $project->stages;

        $allProjectMembers = $project->load('members', 'focalPerson', 'teamLead')->allMembers()->pluck('full_name', 'id');

        $parentActivities = $this->projectActivity->where('project_id', '=', $project->id)->get();

        return view('Project::ProjectActivity.create', compact('activityLevels', 'stages', 'project', 'parentActivities', 'allProjectMembers', 'status'));
    }

    public function store(StoreRequest $request, Project $project)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['project_id'] = $project->id;
        $inputs['created_by'] = $authUser->id;
        $this->projectActivity->create($inputs);

        return response()->json([
            'message' => 'Project Activity created successfully.',
            'redirect' => route('project.show', $project->id),
        ]);
    }

    public function show($id)
    {
        $projectActivity = $this->projectActivity->find($id);
        $projectActivity->load('latestStatusLog');
        $activityLevels = ActivityLevel::cases();
        $stages = $projectActivity->project?->stages ?? [];
        $parentActivities = $this->projectActivity->where('project_id', '=', $projectActivity->project_id)->get();
        $project = $projectActivity->project;
        $authUser = auth()->user();

        return view('Project::ProjectActivity.show', compact('projectActivity', 'activityLevels', 'stages', 'parentActivities', 'project', 'authUser'));
    }

    public function edit($id)
    {
        $projectActivity = $this->projectActivity->find($id);
        $activityLevels = ActivityLevel::cases();
        $status = ActivityStatus::cases();
        $stages = $projectActivity->project?->stages ?? [];
        $parentActivities = $this->projectActivity->where('project_id', '=', $projectActivity->project_id)->get();
        $project = $projectActivity->project;

        $allProjectMembers = $project->load('members', 'focalPerson', 'teamLead')->allMembers()->pluck('full_name', 'id');

        return view('Project::ProjectActivity.edit', compact('projectActivity', 'activityLevels', 'stages', 'parentActivities', 'project', 'allProjectMembers', 'status'));
    }

    public function update(UpdateRequest $request, ProjectActivity $projectActivity)
    {
        $authUser = auth()->user();
        $input = $request->validated();
        $input['project_id'] = $projectActivity->project_id;
        $input['updated_by'] = $authUser->id;
        $this->projectActivity->update($projectActivity->id, $input);

        return response()->json([
            'message' => 'Project Activity updated successfully.',
            'redirect' => route('project.show', $projectActivity->project_id),
        ]);
    }

    public function destroy(ProjectActivity $projectActivity)
    {
        $projectId = $projectActivity->project_id;
        $projectActivity = $this->projectActivity->destroy($projectActivity->id);
        if ($projectActivity) {
            return response()->json([
                'type' => 'success',
                'message' => 'Project Activity is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Project Activity can not deleted.',
            'redirect' => route('project.show', $projectId),
        ], 422);
    }

    public function updateStatus(Request $request, ProjectActivity $projectActivity)
    {
        $request->validate([
            'status' => 'required|string|in:not_started,under_progress,no_required,completed',
            'remarks' => 'nullable|string',
            'status_date' => 'nullable|date',
        ]);

        $oldStatus = $projectActivity->status;
        $newStatus = $request->input('status');
        $statusDate = $request->input('status_date') ?? now();

        // Update actual_start_date and actual_completion_date based on status
        if ($newStatus == ActivityStatus::UnderProgress->value && !$projectActivity->actual_start_date) {
            $projectActivity->actual_start_date = $statusDate;
        }
        if (($newStatus == ActivityStatus::Completed->value || $newStatus == ActivityStatus::NoRequired->value)) {
            $projectActivity->actual_completion_date = $statusDate;
            if (!$projectActivity->actual_start_date) {
                $projectActivity->actual_start_date = $statusDate;
            }
        }
        if ($newStatus == ActivityStatus::NotStarted->value) {
            $projectActivity->actual_start_date = null;
            $projectActivity->actual_completion_date = null;
        }

        $projectActivity->status = $newStatus;
        $projectActivity->save();

        // Log status change
        ProjectActivityStatusLog::create([
            'project_activity_id' => $projectActivity->id,
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'remarks' => $request->input('remarks'),
        ]);

        // Propagate status to parent if needed
        $this->updateParentActivity($projectActivity, $statusDate);

        return response()->json([
            'message' => 'Project Activity status updated successfully.',
        ]);
    }

    /**
     * Update parent activity status and actual dates based on children.
     *
     * @param ProjectActivity $projectActivity
     * @param string|\Carbon\Carbon $statusDate
     */
    public function updateParentActivity(ProjectActivity $projectActivity, $statusDate = null)
    {
        // Only update parent if there is one
        if (!$projectActivity->parent_id) {
            return;
        }

        $parent = $projectActivity->parent;
        if (!$parent) {
            return;
        }

        $children = $parent->children;
        $statuses = $children->pluck('status')->unique();

        $oldParentStatus = $parent->status;
        $newParentStatus = null;

        // If all children have the same status, propagate to parent
        if ($statuses->count() === 1) {
            $newParentStatus = $statuses->first();
        } else {
            // If any child is under_progress, parent should be under_progress
            if ($children->contains('status', ActivityStatus::UnderProgress->value)) {
                $newParentStatus = ActivityStatus::UnderProgress->value;
            } elseif ($children->contains('status', ActivityStatus::Completed->value) || $children->contains('status', ActivityStatus::NoRequired->value)) {
                // If all children are completed or no_required, set parent accordingly
                if (
                    $children->every(function ($c) {
                        return in_array($c->status, [ActivityStatus::Completed->value, ActivityStatus::NoRequired->value]);
                    })
                ) {
                    $newParentStatus = ActivityStatus::Completed->value;
                }
            } elseif ($children->contains('status', ActivityStatus::NotStarted->value)) {
                $newParentStatus = ActivityStatus::NotStarted->value;
            }
        }

        if ($newParentStatus && $oldParentStatus !== $newParentStatus) {
            // Update actual dates for parent
            if ($newParentStatus == ActivityStatus::UnderProgress->value && !$parent->actual_start_date) {
                $parent->actual_start_date = $statusDate ?? now();
            }
            if (in_array($newParentStatus, [ActivityStatus::Completed->value, ActivityStatus::NoRequired->value])) {
                $parent->actual_completion_date = $statusDate ?? now();
                if (!$parent->actual_start_date) {
                    $parent->actual_start_date = $statusDate ?? now();
                }
            }
            if ($newParentStatus == ActivityStatus::NotStarted->value) {
                $parent->actual_start_date = null;
                $parent->actual_completion_date = null;
            }

            $parent->status = $newParentStatus;
            $parent->save();

            ProjectActivityStatusLog::create([
                'project_activity_id' => $parent->id,
                'changed_by' => auth()->id(),
                'old_status' => $oldParentStatus,
                'new_status' => $newParentStatus,
                'remarks' => 'Auto-updated based on children status',
            ]);

            // Recursively update up the chain
            $this->updateParentActivity($parent, $statusDate);
        }
    }
}
