<?php

namespace Modules\Project\Controllers;

use Modules\Project\Requests\OtherDetail\StoreRequest;
use Modules\Project\Requests\OtherDetail\UpdateRequest;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ActivityOtherDetailRepository;
use App\Http\Controllers\Controller;

class ProjectActivityOtherDetailsController extends Controller
{
    protected $otherDetailRepo;

    public function __construct(ActivityOtherDetailRepository $otherDetailRepo)
    {
        $this->otherDetailRepo = $otherDetailRepo;
    }

    // POST: add a single other detail
    public function store(StoreRequest $request, $projectActivity)
    {
        $data = $request->validated();
        $data['project_activity_id'] = $projectActivity;
        $detail = $this->otherDetailRepo->create($data);
        return response()->json([
            'message' => 'Other detail added successfully.',
            'detail' => $detail
        ]);
    }

    // PUT: update a single other detail
    public function updateDetail(UpdateRequest $request, $id)
    {
        $data = $request->validated();
        $detail = $this->otherDetailRepo->find($id);
        if (!$detail) {
            return response()->json([
                'message' => 'Other detail not found.'
            ], 404);
        }
        $this->otherDetailRepo->update($detail, $data);
        return response()->json([
            'message' => 'Other detail updated successfully.',
            'detail' => $detail
        ]);
    }

    // DELETE: delete a single other detail
    public function destroy($id)
    {
        $detail = $this->otherDetailRepo->find($id);

        if (!$detail) {
            return response()->json([
                'message' => 'Other detail not found.'
            ], 404);
        }
        $this->otherDetailRepo->delete($detail);
        return response()->json([
            'message' => 'Other detail deleted successfully.'
        ]);
    }
}
