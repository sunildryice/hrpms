<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\ProjectInformation\StoreRequest;
use Modules\Project\Requests\ProjectInformation\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class ProjectController
{

    public function __construct(
        protected ProjectRepository $projectRepository,
        protected UserRepository $userRepository,
    ) {}

    public function index(Request $request)
    {

        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->projectRepository->query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('project.show', $row->id) . '" rel="tooltip" title="View Project">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    $btn .= ' <a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('project.edit', $row->id) . '" rel="tooltip" title="Edit Project">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';

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
        return view('Project::Project.create', compact('authUser'));
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $project = $this->projectRepository->create($inputs);


        return redirect()->route('project.edit', $project->id)->withSuccessMessage('Project created successfully.');
    }

    public function show($id)
    {
        $project = $this->projectRepository->find($id);
        return view('Project::Project.show', compact('project'));
    }


    public function edit($id)
    {
        $project = $this->projectRepository->with('members')->find($id);
        $users = $this->userRepository->pluck('full_name', 'id');
        return view('Project::Project.edit', compact('project', 'users'));
    }

    public function update($id, UpdateRequest $request)
    {
        $inputs = $request->validated();

        $this->projectRepository->update($id, $inputs);

        return redirect()->route('project.index')->withSuccessMessage('Project updated successfully.');
    }
}
