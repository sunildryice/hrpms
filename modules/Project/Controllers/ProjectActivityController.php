<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Requests\ProjectActivity\StoreRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Requests\ProjectActivity\UpdateRequest;
use Illuminate\Support\Facades\Gate;

class ProjectActivityController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    ) {}
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
            ->withCount('timesheets');

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
            ->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('project-activity.show', $row->id) . '" rel="tooltip" title="View Project Activity">';
                $btn .= '<i class="bi bi-eye"></i></a>';

                if (Gate::allows('manage-project-activity-on-certain-time', $row->project)) {
                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-project-activity-modal-form " href="';
                    $btn .= route('project-activity.edit', $row->id) . '" rel="tooltip" title="Edit Project Activity">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';


                    $btn .= ' <button class="btn btn-outline-danger btn-sm delete-project-activity delete-record"
                data-href="';
                    $btn .= route('project-activity.destroy', $row->id) . '"
                data-id="';
                    $btn .= $row->id . '" rel="tooltip" title="Delete Project Activity">';
                    $btn .= '<i class="bi bi-trash"></i></button>';
                }
                if ($row->activity_level !== ActivityLevel::Theme->value) {
                    // $isAssigned = $row->isUserAssignedToActivity($authUser->id, $row->id);
                    // if ($isAssigned) {
                    if ($row->timesheets_count === 0) {
                        $btn .= ' <a class="btn btn-outline-info btn-sm open-timesheet-modal-form" href="' . route('project-activity.timesheet.create', $row->id) . '" rel="tooltip" title="Add Timesheet">';
                        $btn .= '<i class="bi bi-clock"></i></a>';
                    }
                    // }
                }

                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function create(Project $project)
    {
        $activityLevels = ActivityLevel::cases();
        $stages = $project->stages;

        $parentActivities = $this->projectActivity->where('project_id', '=', $project->id)->get();

        return view('Project::ProjectActivity.create', compact('activityLevels', 'stages', 'project', 'parentActivities'));
    }

    public function store(StoreRequest $request, Project $project)
    {
        $authUser = auth()->user();
        $input = $request->validated();
        $input['project_id'] = $project->id;
        $inputs['created_by'] = $authUser->id;
        $this->projectActivity->create($input);

        return response()->json([
            'message' => 'Project Activity created successfully.',
            'redirect' => route('project.show', $project->id),
        ]);
    }

    public function show($id)
    {
        $projectActivity = $this->projectActivity->find($id);
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
        $stages = $projectActivity->project?->stages ?? [];
        $parentActivities = $this->projectActivity->where('project_id', '=', $projectActivity->project_id)->get();
        $project = $projectActivity->project;

        return view('Project::ProjectActivity.edit', compact('projectActivity', 'activityLevels', 'stages', 'parentActivities', 'project'));
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
}
