<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ProjectActivityDetail;
use Modules\Project\Repositories\ProjectActivityDetailRepository;
use Modules\Project\Requests\ProjectActivityDetail\StoreRequest;
use Modules\Project\Requests\ProjectActivityDetail\UpdateRequest;

class ProjectActivityDetailController extends Controller
{
    public function __construct(
        protected ProjectActivityDetailRepository $detailRepo
    ) {}

    public function create($projectActivity)
    {
        $projectActivity = ProjectActivity::with('project')->findOrFail($projectActivity);

        $this->authorize('manage-project-activity-detail', $projectActivity->project);

        return view('Project::ProjectActivityDetail.create', compact('projectActivity'));
    }

    public function edit($id)
    {
        $detail = ProjectActivityDetail::with('projectActivity.project')->findOrFail($id);

        $this->authorize('manage-project-activity-detail', $detail->projectActivity->project);

        return view('Project::ProjectActivityDetail.edit', compact('detail'));
    }

    public function store(StoreRequest $request, $projectActivity)
    {
        $projectActivity = ProjectActivity::with('project')->findOrFail($projectActivity);

        $this->authorize('manage-project-activity-detail', $projectActivity->project);

        $data = $request->validated();
        $data['project_activity_id'] = $projectActivity->id;
        $data['created_by'] = auth()->id();

        $detail = $this->detailRepo->create($data);

        return response()->json([
            'message' => 'Details added successfully.',
            'detail' => [
                'id' => $detail->id,
                'key_accomplishment' => $detail->key_accomplishment,
                'challenge' => $detail->challenge,
                'lesson_learned' => $detail->lesson_learned,
            ],
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $detail = ProjectActivityDetail::with('projectActivity.project')->findOrFail($id);

        $this->authorize('manage-project-activity-detail', $detail->projectActivity->project);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $detail = $this->detailRepo->update($id, $data);

        return response()->json([
            'message' => 'Details updated successfully.',
            'detail' => [
                'id' => $detail->id,
                'key_accomplishment' => $detail->key_accomplishment,
                'challenge' => $detail->challenge,
                'lesson_learned' => $detail->lesson_learned,
            ],
        ]);
    }

    public function destroy($id)
    {
        $detail = ProjectActivityDetail::with('projectActivity.project')->findOrFail($id);

        $this->authorize('manage-project-activity-detail', $detail->projectActivity->project);

        $this->detailRepo->destroy($id);

        return response()->json([
            'message' => 'Details deleted successfully.',
        ]);
    }
}
