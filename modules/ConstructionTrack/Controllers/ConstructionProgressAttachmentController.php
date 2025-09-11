<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ConstructionTrack\Models\ConstructionProgress;
use Modules\ConstructionTrack\Models\ConstructionProgressAttachment;
use Modules\ConstructionTrack\Repositories\ConstructionProgressRepository;
use Modules\ConstructionTrack\Requests\ConstructionProgressAttachment\StoreRequest;

class ConstructionProgressAttachmentController extends Controller
{
    public function __construct(
        ConstructionProgressRepository $constructionProgress
    )
    {
        $this->constructionProgress = $constructionProgress;
        $this->destinationPath      = 'constructionProgress';
    }

    public function index($progressId)
    {
        $constructionProgress = $this->constructionProgress->find($progressId);

        $array = [
            'attachments'   => $constructionProgress->getAttachments()
        ];

        return view('ConstructionTrack::ConstructionProgressAttachment.index', $array);
    }

    public function create($progressId)
    {
        $constructionProgress = $this->constructionProgress->find($progressId);

        $construction = $constructionProgress->construction;
        $this->authorize('addProgress', $construction);

        $array = [
            'progressId'        => $progressId,
            'constructionId'    => $constructionProgress->construction_id,
            'attachments'       => $constructionProgress->getAttachments()
        ];

        return view('ConstructionTrack::ConstructionProgressAttachment.create', $array);
    }

    public function store(StoreRequest $request, $progressId)
    {
        $inputs = $request->validated();

        $constructionProgress = $this->constructionProgress->find($progressId);

        $construction = $constructionProgress->construction;
        $this->authorize('addProgress', $construction);

        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                                    ->storeAs($this->destinationPath, time().'_'.random_int(1000, 9999).'_construction_progress_attachment.'.$request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
            }
            $inputs['created_by'] = auth()->user()->id;
            $record = $constructionProgress->attachments()->create($inputs);
            DB::commit();
            if ($record) {
                return redirect()->back()->withSuccessMessage('File uploaded successfully.');
            } else {
                return redirect()->back()->withInput()->withWarningMessage('File could not be uploaded.');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->withWarningMessage('File could not be uploaded.');
        }
    }

    public function destroy($attachmentId)
    {
        DB::beginTransaction();
        try {
            $constructionProgressAttachment = ConstructionProgressAttachment::findOrFail($attachmentId);
            $constructionProgressAttachment->delete();
            DB::commit();
            return redirect()->back()->withSuccessMessage('File deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->withWarningMessage('File could not be deleted.');
        }
    }
}