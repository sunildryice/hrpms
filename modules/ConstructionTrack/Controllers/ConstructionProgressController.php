<?php

namespace Modules\ConstructionTrack\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ConstructionTrack\Requests\ConstructionProgress\StoreRequest;
use Modules\ConstructionTrack\Requests\ConstructionProgress\UpdateRequest;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\ConstructionTrack\Repositories\ConstructionProgressRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ConstructionProgressController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ConstructionRepository $constructions
     * @param FiscalYearRepository $fiscalYears
     * @param ConstructionProgressRepository $constructionProgress
     */
    public function __construct(
        ConstructionRepository        $constructions,
        FiscalYearRepository     $fiscalYears,
        ConstructionProgressRepository $constructionProgress,
    )
    {

        $this->constructions = $constructions;
        $this->constructionProgress = $constructionProgress;
        $this->fiscalYears = $fiscalYears;
        $this->destinationPath = 'constructionProgress';
    }

    /**
     * Display a listing of the construction progress
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $constructionId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $construction = $this->constructions->find($constructionId);
            $data = $this->constructionProgress->select([
                            'id', 
                            'construction_id',
                            'report_date',
                            // 'work_start_date',
                            // 'work_completion_date',
                            'progress_percentage',
                            'estimate',
                            'attachment',
                            'status_id', 
                            'remarks'])
                            ->orderBy('id','asc')
                            ->whereConstructionId($constructionId)->get();

            foreach ($data as $item) {
                $item->estimate = floatval(implode('', explode(',', $item->estimate)));
            }

            $lastData = $this->constructionProgress->orderby('id','DESC')->where('construction_id','=',$construction->id)->first();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('report_date', function ($row) {
                return $row->report_date?->toFormattedDateString();
            })
            ->addColumn('progress_percentage', function ($row) {
                return $row->progress_percentage.'%';
            })
            ->addColumn('remarks', function ($row) {
                return $row->remarks;
            })
            ->addColumn('attachment', function ($row) {
                if ($row->getAttachments()->isNotEmpty()) {
                    $btn_open = '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn_open .= route('construction.progress.attachment.index', $row->id).'" rel="tooltip" title="Attachments"><i class="bi bi-eye"></i></a>';
                    return $btn_open;
                }
            })
            ->addColumn('action', function ($row) use ($authUser, $construction, $lastData) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-progress-modal-form" href="';
                if($row->id == $lastData->id){    
                    if ($authUser->can('edit', $row)) {
                        $btn .= route('construction.progress.edit', [$row->construction_id, $row->id]) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }
                
                    if ($authUser->can('manageAttachment', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('construction.progress.attachment.create', $row->id).'" rel="tooltip" title="Add Attachments"><i class="bi bi-paperclip"></i></a>';
                    }

                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('construction.progress.destroy', [$row->construction_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>'; 
                    }
                }       
                return $btn;
            })
            ->withQuery('total_progress_percentage', function ($filteredQuery) {
                return $filteredQuery->last('progress_percentage');
            })->withQuery('total_estimate', function ($filteredQuery) {
                return $filteredQuery->sum('estimate');
            })
            ->rawColumns(['attachment', 'action'])
            ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new construction request detail by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        return view('ConstructionTrack::ConstructionProgress.create')
            ->withConstruction($construction);
    }

    /**
     * Store a newly created construction Progress in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $this->authorize('addProgress', $construction);
        $inputs = $request->validated();
        $inputs['construction_id'] = $construction->id;

        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                                        ->where('end_date', '>=', date('Y-m-d'))
                                        ->first();    

        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;    
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;    
        $constructionProgress = $this->constructionProgress->create($inputs);

        if ($constructionProgress) {
            return response()->json(['status' => 'ok',
                'constructionProgress' => $constructionProgress,
                'message' => 'Construction Progress is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Construction Progress can not be added.'], 422);

    }


    /**
     * Show the form for editing the specified advance request detail.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        $constructions = $this->constructions->find($prId);
        $constructionProgress = $this->constructionProgress->find($id);
        // $this->authorize('update', $constructions);

        return view('ConstructionTrack::ConstructionProgress.edit')
            ->withConstructionProgress($constructionProgress)
            ->withConstruction($constructions);
    }


    /**
     * Update the specified Construction Progress in storage.
     *
     * @param \Modules\ConstructionTrack\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $constructionProgress = $this->constructionProgress->find($id);
        $inputs = $request->validated();

        $sumOfProgressPercentage = $this->constructionProgress
                                        ->where('construction_id', '=' ,$constructionProgress->construction_id)
                                        ->where('id', '!=', $id)
                                        ->sum('progress_percentage');

        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                                        ->where('end_date', '>=', date('Y-m-d'))
                                        ->first();


        $inputs['requester_id'] = auth()->id();
        $inputs['updated_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $constructionProgress = $this->constructionProgress->update($id, $inputs);

        if ($constructionProgress) {
            return response()->json([
                'status'                => 'ok',
                'constructionProgress'  => $constructionProgress,
                'message'               => 'Construction Progress is successfully updated.'
            ], 200);
        }

        return response()->json([
            'status'    => 'error',
            'message'   => 'Construction Progress can not be updated.'
        ], 422);
    }


    /**
     * Remove the specified Construction Progress from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        // $this->authorize('delete', $constructionProgress->advanceRequest);
        $flag = $this->constructionProgress->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Construction Progress is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Construction Progress can not deleted.',
        ], 422);
    }

}
