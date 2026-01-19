<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Requests\ProjectActivity\StoreRequest;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Project;

class ProjectActivityController extends Controller
{
    public function create(Project $project)
    {
        $activityLevels = ActivityLevel::cases();
        $stages = $project->stages;
        // $parentActivities = $project->activities;
        // dd($parentActivities);

        return view('Project::ProjectActivity.create', compact('activityLevels', 'stages'));
    }

    public function store(StoreRequest $request)
    {
        dd($request->all());
    }
}
