<?php

namespace Modules\Attachment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Attachment\Repositories\AttachmentRepository;
use Modules\Attachment\Requests\StoreRequest;
use Modules\Attachment\Requests\UpdateRequest;
use ReflectionClass;
use Yajra\DataTables\DataTables;

class AttachmentController extends Controller
{
    private $attachments;
    private $destinationPath;

    public function __construct(
        AttachmentRepository  $attachments,
    )
    {
        $this->attachments    = $attachments;
        $this->destinationPath  = 'attachment';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $modelType = $request->modelType;
        $modelId = $request->modelId;

        if ($request->ajax()) {
            $reflection = new ReflectionClass($modelType);
            $modelNamespaceName = $reflection->getName();
            $model = $modelNamespaceName::findOrFail($modelId);

            $data = $model->attachments;

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
                    $btn .= route('attachments.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('attachments.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['attachment', 'action', 'link'])
                ->make(true);
        }

        return view('Attachment::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $modelType = $request->modelType;
        $modelId = $request->modelId;

        return view('Attachment::create')
        ->withModelId($modelId)
        ->withModelType($modelType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $modelType = $request->modelType;
        $modelId = $request->modelId;

        $inputs = $request->validated();

        $reflection = new ReflectionClass($modelType);

        $modelClassName = $reflection->getShortName();
        $modelNamespaceName = $reflection->getName();

        $model = $modelNamespaceName::findOrFail($modelId);

        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                        ->storeAs($this->destinationPath . '/'. $modelClassName, time().'_'.random_int(1000, 9999).'.'.$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }

        $inputs['created_by'] = auth()->user()->id;
        $attachment = $this->attachments->create($model, $inputs);

        if ($attachment) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attachment added successfully!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be added.'
            ], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($attachment)
    {
        $attachment = $this->attachments->find($attachment);
        return view('Attachment::edit')
        ->withAttachment($attachment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $attachment)
    {
        $attachment = $this->attachments->find($attachment);

        $inputs = $request->validated();

        $modelClassName = class_basename($attachment->attachmentable);

        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                        ->storeAs($this->destinationPath . '/'. $modelClassName, time().'_'.random_int(1000, 9999).'.'.$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }

        $inputs['updated_by'] = auth()->user()->id;
        $attachment = $this->attachments->update($attachment, $inputs);

        if ($attachment) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attachment updated successfully!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment cannot be updated.'
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($attachment)
    {
        $attachment = $this->attachments->find($attachment);
        $attachment = $this->attachments->destroy($attachment);

        if ($attachment) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Attachment deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Attachment could not be deleted.'
            ], 422);
        }
    }

    public function list(Request $request)
    {
        $authUser = auth()->user();

        $modelType = $request->modelType;
        $modelId = $request->modelId;

        if ($request->ajax()) {
            $reflection = new ReflectionClass($modelType);
            $modelNamespaceName = $reflection->getName();
            $model = $modelNamespaceName::findOrFail($modelId);

            $data = $model->attachments;

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
                ->rawColumns(['attachment', 'link'])
                ->make(true);
        }

        return view('Attachment::list');
    }
}
