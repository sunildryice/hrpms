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
        $data = $request->validated();

        $data['activity_id'] = $projectActivity->id;
        $data['project_id'] = $projectActivity->project_id;
        $data['created_by'] = auth()->id();

        $extension = $this->projectActivityExtension->create($data);

        $projectActivity->update([
            'actual_completion_date' => $extension->extended_completion_date,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Extension added successfully.',
        ]);
    }
}