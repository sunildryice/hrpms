<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet; // ← add this
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Requests\ActivityTimeSheet\StoreRequest;
use Modules\Project\Requests\ActivityTimeSheet\UpdateRequest;

class ProjectActivityTimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets
    ) {
    }

    public function create(ProjectActivity $projectActivity)
    {
        $timesheet = null;
        return view('Project::ProjectActivityTimeSheet.create', compact('projectActivity', 'timesheet'));
    }

    public function edit(ActivityTimeSheet $timesheet)
    {
        $projectActivity = $timesheet->activity;
        return view('Project::ProjectActivityTimeSheet.create', compact('projectActivity', 'timesheet'));
    }


    public function store(StoreRequest $request, ProjectActivity $projectActivity)
    {
        $inputs = $request->validated();
        $inputs['activity_id'] = $projectActivity->id;
        $inputs['project_id'] = $projectActivity->project_id;
        $inputs['created_by'] = auth()->id();

        $this->activityTimeSheets->create($inputs);

        return response()->json([
            'message' => 'Timesheet created successfully.',
            'redirect' => route('project.show', $projectActivity->project_id),
        ]);
    }

    public function update(UpdateRequest $request, ActivityTimeSheet $timesheet)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $this->activityTimeSheets->update($timesheet->id, $inputs);

        return response()->json([
            'message' => 'Timesheet updated successfully.',
            'redirect' => route('project.show', $timesheet->project_id),
        ]);
    }
}