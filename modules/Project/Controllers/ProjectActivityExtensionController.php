<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\TimeSheet\StoreRequest;
use Modules\Project\Requests\TimeSheet\UpdateRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class ProjectActivityExtensionController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
    ) {
    }

    public function create()
    {
        $authUser = auth()->user();
        return view('Project::ProjectActivityExtension.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();

        $this->timeSheets->create($inputs);

        return response()->json([
            'message' => 'Project Activity Extension created successfully.',
        ]);
    }
}