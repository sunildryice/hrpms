<?php

namespace Modules\Project\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ProjectRepository;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectRepository         $projects,
        protected ProjectActivityRepository $projectActivities,
    )
    {
    }

    public function show(Request $request, $projectId)
    {
        $authUser = auth()->user();
        $project = $this->projects->with(['members' => function ($query) {
            $query->select('id', 'full_name')
                ->whereNotNull('activated_at');
        }])->find($projectId);

        $assignedActivities = $this->projectActivities->getActivitiesByProjectId($authUser, $projectId);

        return response()->json([
            'assignedActivities' => $assignedActivities,
            'members' => $project->members,
            'project' => $project,
        ]);
    }
}
