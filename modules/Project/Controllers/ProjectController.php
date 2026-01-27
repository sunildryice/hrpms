<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\ActivityStageRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\Project\StoreRequest;
use Modules\Project\Requests\Project\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class ProjectController
{
    public function __construct(
        protected ProjectRepository       $projectRepository,
        protected UserRepository          $userRepository,
        protected ActivityStageRepository $activityStageRepository,
    )
    {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->projectRepository->query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('start_date', function ($row) {
                    return $row->formatted_start_date;
                })
                ->editColumn('completion_date', function ($row) {
                    return $row->formatted_completion_date;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('project.show', $row->id) . '" rel="tooltip" title="View Project">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-pms')) {
                        $btn .= ' <a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('project.edit', $row->id) . '" rel="tooltip" title="Edit Project">';
                        $btn .= '<i class="bi bi-pencil-square"></i></a>';

                        $btn .= ' <button class="btn btn-outline-danger btn-sm delete-project delete-record"
                        data-href="' . route('project.destroy', $row->id) . '"
                        data-id="' . $row->id . '" rel="tooltip" title="Delete Project">';
                        $btn .= '<i class="bi bi-trash"></i></button>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Project::Project.index');
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
