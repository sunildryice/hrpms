<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\ActivityStageRepository;
use Modules\Project\Repositories\ProjectRepository;

class ProjectGanttChartController
{
    public function __construct(
        protected ProjectRepository $projectRepository,
        protected UserRepository $userRepository,
        protected ActivityStageRepository $activityStageRepository,
    ) {}

    public function index(Request $request, $id)
    {
        $project = $this->projectRepository->find($id);
        $stages = $this->activityStageRepository->all();
        $projectActivity = $project->activities;
        $authUser = auth()->user();

        return view('Project::ProjectGanttChart.index', compact('project', 'stages', 'authUser', 'projectActivity'));
    }
}
