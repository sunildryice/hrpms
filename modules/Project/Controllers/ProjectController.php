<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Requests\Project\StoreRequest;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\Project\UpdateRequest;
use Modules\Project\Repositories\ActivityStageRepository;

class ProjectController
{
    public function __construct(
        protected ProjectRepository $projectRepository,
        protected UserRepository $userRepository,
        protected ActivityStageRepository $activityStageRepository,
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->projectRepository
                ->when(!$authUser->can('manage-pms'), function ($q) use ($authUser) {
                    $q->where(function ($sq) use ($authUser) {
                        $sq->where('focal_person_id', $authUser->id)
                            ->orWhere('team_lead_id', $authUser->id)
                            ->orWhereHas('members', function ($tq) use ($authUser) {
                                $tq->where('user_id', $authUser->id);
                            });
                    });
                });
            if ($request->active) {
                $data->whereNotNull('activated_at');
            } else {
                $data->whereNull('activated_at');
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('start_date', function ($row) {
                    return $row->formatted_start_date;
                })
                ->editColumn('completion_date', function ($row) {
                    return $row->formatted_completion_date;
                })
                ->editColumn('team_lead_id', function ($row) {
                    return $row->teamLead ? $row->teamLead->full_name : '-';
                })
                ->editColumn('focal_person_id', function ($row) {
                    return $row->focalPerson ? $row->focalPerson->full_name : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->getActiveStatus();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('project.dashboard', $row->id) . '" rel="tooltip" title="View Project">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-pms')) {
                        $btn .= ' <a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('project.edit', $row->id) . '" rel="tooltip" title="Edit Project">';
                        $btn .= '<i class="bi bi-pencil-square"></i></a>';

                        // $btn .= ' <button class="btn btn-outline-danger btn-sm delete-project delete-record"
                        // data-href="' . route('project.destroy', $row->id) . '"
                        // data-id="' . $row->id . '" rel="tooltip" title="Delete Project">';
                        // $btn .= '<i class="bi bi-trash"></i></button>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $requestData = $request->all();

        return view('Project::Project.index', compact('requestData'));
    }

    public function create()
    {
        $authUser = auth()->user();
        $users = $this->userRepository->pluck('full_name', 'id');
        $project = Project::with('members')->getModel();
        $stages = $this->activityStageRepository->all();
        return view('Project::Project.create', compact('authUser', 'users', 'stages'));
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $project = $this->projectRepository->create($inputs);
        if ($project) {
            return redirect()->route('project.index')->withSuccessMessage('Project created successfully.');
        }
        return redirect()->back()->withInput()->withErrorMessage('Failed to create Project.');
    }

    public function show($id)
    {
        $project = $this->projectRepository->find($id);
        $users = $this->userRepository->pluck('full_name', 'id');
        $stages = $this->activityStageRepository->all();
        $projectActivity = $project->activities;
        $authUser = auth()->user();
        return view('Project::Project.show', compact('project', 'users', 'stages', 'authUser', 'projectActivity'));
    }

    public function dashboard(Request $request, $id)
    {
        $users = $this->userRepository->pluck('full_name', 'id');
        $project = $this->projectRepository->find($id);

        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // Aggregate activities excluding theme level
        $activitiesQuery = $project->activities()
            ->where('activity_level', '!=', ActivityLevel::Theme->value);

        if ($fromDate && $toDate) {
            $activitiesQuery->whereBetween('completion_date', [$fromDate, $toDate]);
        }

        $statusCounts = $activitiesQuery
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusDistribution = [
            ActivityStatus::Completed->value => $statusCounts[ActivityStatus::Completed->value] ?? 0,
            ActivityStatus::UnderProgress->value => $statusCounts[ActivityStatus::UnderProgress->value] ?? 0,
            ActivityStatus::NotStarted->value => $statusCounts[ActivityStatus::NotStarted->value] ?? 0,
            ActivityStatus::NoRequired->value => $statusCounts[ActivityStatus::NoRequired->value] ?? 0,
        ];

        $totalActivities = array_sum($statusDistribution);

        // Calculate percentages (avoid division by zero)
        if ($totalActivities > 0) {
            $percentages = [
                'completed' => round(($statusDistribution[ActivityStatus::Completed->value] / $totalActivities) * 100, 1),
                'under_progress' => round(($statusDistribution[ActivityStatus::UnderProgress->value] / $totalActivities) * 100, 1),
                'not_started' => round(($statusDistribution[ActivityStatus::NotStarted->value] / $totalActivities) * 100, 1),
                'no_required' => round(($statusDistribution[ActivityStatus::NoRequired->value] / $totalActivities) * 100, 1),
            ];
        } else {
            $percentages = [
                'completed' => 0,
                'under_progress' => 0,
                'not_started' => 0,
                'no_required' => 0,
            ];
        }

        $completionRate = $percentages['completed'];
        $totalStages = $project->stages()->count();
        $totalMembers = $project->members()->count();

        return view('Project::Project.dashboard', compact(
            'project',
            'statusDistribution',
            'percentages',
            'totalActivities',
            'completionRate',
            'totalStages',
            'totalMembers',
            'users',
            'fromDate',
            'toDate',
        ));
    }

    public function edit($id)
    {
        $project = $this->projectRepository->with(['members', 'stages'])->find($id);
        $users = $this->userRepository->pluck('full_name', 'id');
        $stages = $this->activityStageRepository->all();
        return view('Project::Project.edit', compact('project', 'users', 'stages'));
    }

    public function update($id, UpdateRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : null;
        $inputs['show_pms_dashboard'] = $request->has('show_pms_dashboard') ? 1 : 0;
        $project = $this->projectRepository->update($id, $inputs);
        if ($project) {
            return redirect()->route('project.index')->withSuccessMessage('Project updated successfully.');
        }
        return redirect()->back()->withInput()->withErrorMessage('Failed to update Project.');
    }

    public function destroy($id)
    {
        $project = $this->projectRepository->destroy($id);
        if ($project) {
            return response()->json([
                'type' => 'success',
                'message' => 'Project is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Project can not deleted.',
        ], 422);
    }
}
