<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Requests\ProjectActivity\StoreRequest;
use Modules\Project\Requests\ProjectActivity\UpdateRequest;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\ProjectActivityRepository;

class ProjectActivityController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    ) {}

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
        $input = $request->validated();
        $this->projectActivity->create($input);

        return redirect()->route('project.show', $input['project_id'])
            ->with('success_message', 'Project Activity created successfully.');
    }

    public function edit($id)
    {
        $projectActivity = $this->projectActivity->find($id);
        $activityLevels = ActivityLevel::cases();
        $stages = $projectActivity->project->stages;
        // $parentActivities = $projectActivity->project->activities;

        return view('Project::ProjectActivity.edit', compact('projectActivity', 'activityLevels', 'stages'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $input = $request->validated();
        $this->projectActivity->update($id, $input);

        return redirect()->route('project.show', $input['project_id'])
            ->with('success_message', 'Project Activity updated successfully.');
    }

    public function destroy($id)
    {
        $this->projectActivity->destroy($id);

        return redirect()->route('project.show', $id)
            ->with('success_message', 'Project Activity deleted successfully.');
    }
}
