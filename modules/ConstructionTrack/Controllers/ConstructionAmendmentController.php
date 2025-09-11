<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ConstructionTrack\Repositories\ConstructionAmendmentRepository;
use Modules\ConstructionTrack\Requests\ConstructionAmendment\StoreRequest;
use Modules\ConstructionTrack\Requests\ConstructionAmendment\UpdateRequest;
use Yajra\DataTables\DataTables;


class ConstructionAmendmentController extends Controller
{
    private $constructionAmendments;
    private $destinationPath;
    public function __construct(
        ConstructionAmendmentRepository $constructionAmendments
    )
    {
        $this->constructionAmendments = $constructionAmendments;
        $this->destinationPath = 'constructionAmendment';
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @param mixed $constructionId
     * @return mixed
     * 
     */
    public function index(Request $request, $constructionId)
    {
        if ($request->ajax()) {
            $data = $this->constructionAmendments
            ->where('construction_id', '=', $constructionId)
            ->orderby('created_at', 'asc')
            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('effective_date', function ($row) {
                    return $row->getEffectiveDate();
                })
                ->addColumn('extension_to_date', function ($row) {
                    return $row->extension_to_date?->toFormattedDateString();
                })
                ->addColumn('total_estimate_cost', function ($row) {
                    return $row->getTotalEstimateCost();
                })
                ->addColumn('attachment', function ($row) {
                    if($row->attachment){
                        $btn = '';
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= asset('storage/'.$row->attachment).'" target="_blank" rel="tooltip" title="View attachment"><i class="bi bi-file-earmark-text"></i></a>';
                        return $btn;
                    }
                    return "n/a";
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->toFormattedDateString();
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-construction-amendment-edit-modal-form" href="';
                    $btn .= route('construction.amendment.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('construction.amendment.destroy', $row->id) . '">';
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
     * @param mixed $constructionId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * 
     */
    public function create($constructionId)
    {
        $array = [
            'constructionId' => $constructionId
        ];
        return view('ConstructionTrack::ConstructionAmendment.create', $array);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @param mixed $constructionId
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
            $record = $this->constructionAmendments->create($inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'amendment' => $record,
                    'message' => 'Amendment added successfully.'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Amendment cannot be added.'
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     * @param mixed $amendmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($amendmentId)
    {
        $constructionAmendment = $this->constructionAmendments->find($amendmentId);
        $array = [
            'constructionAmendment' => $constructionAmendment
        ];
        return view('ConstructionTrack::ConstructionAmendment.show', $array);
    }

    /**
     * 
     * Show the form for editing the specified resource.
     * @param mixed $attachmentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * 
     */
    public function edit($amendmentId)
    {
        $constructionAmendment = $this->constructionAmendments->find($amendmentId);
        $array = [
            'constructionAmendment' => $constructionAmendment
        ];
        return view('ConstructionTrack::ConstructionAmendment.edit', $array);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateRequest $request
     * @param mixed $amendmentId
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function update(UpdateRequest $request, $amendmentId)
    {
        $inputs = $request->validated();
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath, $filename);
            }
            $record = $this->constructionAmendments->update($amendmentId, $inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'amendment' => $record,
                    'message' => 'Amendment updated successfully.'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Amendment cannot be updated.'
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $amendmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($amendmentId)
    {
        $deleted = $this->constructionAmendments->destroy($amendmentId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Amendment deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Amendment cannot be deleted.'
            ], 422);
        }
    }
}
