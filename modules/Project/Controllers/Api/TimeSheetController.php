<?php

namespace Modules\Project\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ProjectActivityRepository;


class TimeSheetController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivities,
    ) {
    }


    public function getActivitiesByProject(Request $request)
    {
        $projectId = $request->query('project_id');

        if (!$projectId) {
            return response()->json(['activities' => []]);
        }

        $activities = ProjectActivity::query()
            ->where('project_id', $projectId)
            ->whereIn('activity_level', ['activity', 'sub_activity'])
            ->orderBy('title')
            ->get(['id', 'title']);

        return response()->json([
            'activities' => $activities
        ]);
    }
}
