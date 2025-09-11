<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ConstructionTrack\Repositories\ConstructionAttachmentRepository;
use Modules\ConstructionTrack\Requests\ConstructionAttachment\StoreRequest;
use Modules\ConstructionTrack\Requests\ConstructionAttachment\UpdateRequest;
use Yajra\DataTables\DataTables;

class ConstructionAttachmentController extends Controller
{
    private $constructionAttachments;

    private $destinationPath;

    public function __construct(
        ConstructionAttachmentRepository $constructionAttachments
    ) {
        $this->constructionAttachments = $constructionAttachments;
        $this->destinationPath = 'constructionAttachment';
    }

    /**
     * Display a listing of the resource.
     *
     * @param  mixed  $constructionId
     * @return mixed
     */
    public function index(Request $request, $constructionId)
    {
        if ($request->ajax()) {
            $data = $this->constructionAttachments
                ->where('construction_id', '=', $constructionId)
                ->orderby('created_at', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->getTitle();
                })
                ->addColumn('attachment', function ($row) {
                    $btn = '';
                    if (isset($row->link)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= $row->link.'" target="_blank" rel="tooltip" title="View Link"><i class="bi bi-link-45deg"></i></a>&emsp;';
                    }

                    if (isset($row->attachment)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= asset('storage/'.$row->attachment).'" target="_blank" rel="tooltip" title="View attachment"><i class="bi bi-file-earmark-text"></i></a>';
                    }

                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->toFormattedDateString();
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-construction-attachment-edit-modal-form" href="';
                    $btn .= route('construction.attachment.edit', $row->id).'" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('construction.attachment.destroy', $row->id).'">';
                    $btn .= '<i class="bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['attachment', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  mixed  $constructionId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create($constructionId)
    {
        $array = [
            'constructionId' => $constructionId,
        ];

        return view('ConstructionTrack::ConstructionAttachment.create', $array);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  mixed  $constructionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $constructionId)
    {
        $inputs = $request->validated();
        $inputs['construction_id'] = $constructionId;
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath, $filename);
            }
            $inputs['created_by'] = auth()->user()->id;
            $record = $this->constructionAttachments->create($inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'attachment' => $record,
                    'message' => 'Attachment added successfully.',
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be added.',
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $attachmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($attachmentId)
    {
        $constructionAttachment = $this->constructionAttachments->find($attachmentId);
        $array = [
            'constructionAttachment' => $constructionAttachment,
        ];

        return view('ConstructionTrack::ConstructionAttachment.show', $array);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed  $attachmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($attachmentId)
    {
        $constructionAttachment = $this->constructionAttachments->find($attachmentId);
        $array = [
            'constructionAttachment' => $constructionAttachment,
        ];

        return view('ConstructionTrack::ConstructionAttachment.edit', $array);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  mixed  $constructionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $attachmentId)
    {
        $inputs = $request->validated();
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath, $filename);
            }
            $record = $this->constructionAttachments->update($attachmentId, $inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'attachment' => $record,
                    'message' => 'Attachment updated successfully.',
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be updated.',
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $attachmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($attachmentId)
    {
        $deleted = $this->constructionAttachments->destroy($attachmentId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attachment deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be deleted.',
            ], 422);
        }
    }
}
