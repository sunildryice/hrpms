<?php

namespace Modules\AdvanceRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AdvanceRequest\Requests\SettlementAttachment\StoreRequest;
use Modules\AdvanceRequest\Requests\SettlementAttachment\UpdateRequest;
use Modules\AdvanceRequest\Repositories\SettlementAttachmentRepository;
use Yajra\DataTables\DataTables;


class SettlementAttachmentController extends Controller
{
    private $attachments;
    private $destinationPath;
    public function __construct(
        SettlementAttachmentRepository $attachments
    )
    {
        $this->attachments = $attachments;
        $this->destinationPath = 'advanceSettlementAttachment';
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @param mixed $advanceSettlementId
     * @return mixed
     * 
     */
    public function index(Request $request, $advanceSettlementId)
    {
        if ($request->ajax()) {
            $data = $this->attachments
            ->where('advance_settlement_id', '=', $advanceSettlementId)
            ->orderby('created_at', 'asc')
            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->getTitle();
                })
                ->addColumn('attachment', function ($row) {
                    $btn = '';
                    if (!empty($row->attachment)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= asset('storage/' . $row->attachment) . '" target="_blank" rel="tooltip" title="View attachment"><i class="bi bi-file-earmark-text"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('link', function ($row) {
                    $btn = '';
                    if ($row->link) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= $row->link . '" target="_blank" rel="tooltip" title="Attachment Link"><i class="bi bi-link-45deg"></i></a>';
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
                    $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-attachment-edit-modal-form" href="';
                    $btn .= route('advance.settlement.attachment.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('advance.settlement.attachment.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['attachment', 'action', 'link'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param mixed $advanceSettlementId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * 
     */
    public function create($advanceSettlementId)
    {
        $array = [
            'advanceSettlementId' => $advanceSettlementId
        ];
        return view('AdvanceRequest::SettlementAttachment.create', $array);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @param mixed $advanceSettlementId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $advanceSettlementId)
    {
        $inputs = $request->validated();
        $inputs['advance_settlement_id'] = $advanceSettlementId;
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).'_advance_settlement_attachment'.$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath.'/'.auth()->user()->employee_id, $filename);
            }
            $inputs['created_by'] = auth()->user()->id;
            $record = $this->attachments->create($inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'attachment' => $record,
                    'message' => 'Attachment added successfully.'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be added.'
            ], 422);
        }
    }

    /**
     * 
     * Display the specified resource.
     * @param mixed $attachmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * 
     */
    public function show($attachmentId)
    {
        $attachment = $this->attachments->find($attachmentId);
        $array = [
            'attachment' => $attachment
        ];
        return view('AdvanceRequest::SettlementAttachment.show', $array);
    }

    /**
     * 
     * Show the form for editing the specified resource.
     * @param mixed $attachmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * 
     */
    public function edit($attachmentId)
    {
        $attachment = $this->attachments->find($attachmentId);
        $array = [
            'attachment' => $attachment
        ];
        return view('AdvanceRequest::SettlementAttachment.edit', $array);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateRequest $request
     * @param mixed $constructionId
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function update(UpdateRequest $request, $attachmentId)
    {
        $inputs = $request->validated();
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).'_advance_settlement_attachment'.$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath.'/'.auth()->user()->employee_id, $filename);
            }
            $record = $this->attachments->update($attachmentId, $inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'attachment' => $record,
                    'message' => 'Attachment updated successfully.'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be updated.'
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
        $deleted = $this->attachments->destroy($attachmentId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attachment deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be deleted.'
            ], 422);
        }
    }
}
