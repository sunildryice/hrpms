<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
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
        return view('Project::ProjectActivityDetail.create', compact('projectActivity'));
    }

    public function edit($id)
    {
        $detail = $this->detailRepo->findOrNull($id);

        if (!$detail) {
            abort(404);
        }

        return view('Project::ProjectActivityDetail.edit', compact('detail'));
    }

    public function store(StoreRequest $request, $projectActivity)
    {
        $data = $request->validated();
        $data['project_activity_id'] = $projectActivity;
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
        $this->detailRepo->destroy($id);

        return response()->json([
            'message' => 'Details deleted successfully.',
        ]);
    }
}
