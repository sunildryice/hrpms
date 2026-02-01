<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\ProjectActivityExtension\StoreRequest;
use Modules\Project\Requests\ProjectActivityExtension\UpdateRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ProjectActivityExtensionRepository;

class ProjectActivityExtensionController extends Controller
{
    public function __construct(
        protected ProjectActivityExtensionRepository $projectActivityExtension,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
    ) {
    }

    public function create(ProjectActivity $projectActivity)
    {
        return view('Project::ProjectActivityExtension.create', compact('projectActivity'));
    }

    public function store(StoreRequest $request, ProjectActivity $projectActivity)
    {
        $inputs = $request->validated();
        $inputs['activity_id'] = $projectActivity->id;
        $inputs['project_id'] = $projectActivity->project_id;
        $inputs['created_by'] = auth()->id();

        $this->projectActivityExtension->create($inputs);

        return response()->json([
            'message' => 'Project Activity Extension created successfully.',
        ]);
    }
}