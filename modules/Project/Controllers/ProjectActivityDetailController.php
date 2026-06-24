<?php

namespace Modules\Project\Controllers;

use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ProjectActivityDetailRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectActivityDetailController extends Controller
{
    protected $detailRepo;

    public function __construct(ProjectActivityDetailRepository $detailRepo)
    {
        $this->detailRepo = $detailRepo;
    }

    public function create($projectActivity)
    {
        $projectActivity = ProjectActivity::findOrFail($projectActivity);
        $title = 'Add Details';
        $route = route('project-activity.details.store', $projectActivity->id);
        $method = 'POST';
        $detail = null;

        return view('Project::Partials.detail-form', compact('title', 'route', 'method', 'detail'));
    }

    public function edit($id)
    {
        $detail = $this->detailRepo->find($id);
        if (!$detail) {
            abort(404);
        }
        $title = 'Edit Details';
        $route = route('project-activity.details.update', $detail->id);
        $method = 'PUT';

        return view('Project::Partials.detail-form', compact('title', 'route', 'method', 'detail'));
    }

    public function store(Request $request, $projectActivity)
    {
        $data = $request->validate([
            'key_accomplishment' => 'required|string',
            'challenge' => 'required|string',
            'lesson_learned' => 'required|string',
        ]);

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

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'key_accomplishment' => 'required|string',
            'challenge' => 'required|string',
            'lesson_learned' => 'required|string',
        ]);

        $detail = $this->detailRepo->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Detail not found.'], 404);
        }

        $data['updated_by'] = auth()->id();
        $this->detailRepo->update($detail, $data);

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
        $detail = $this->detailRepo->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Detail not found.'], 404);
        }

        $this->detailRepo->delete($detail);

        return response()->json([
            'message' => 'Details deleted successfully.',
        ]);
    }
}
