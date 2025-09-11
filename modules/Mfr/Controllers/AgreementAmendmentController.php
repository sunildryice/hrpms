<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Mfr\Requests\Agreement\Amendment\StoreRequest;
use Modules\Mfr\Requests\Agreement\Amendment\UpdateRequest;
use Modules\Mfr\Repositories\AgreementAmendmentRepository;
use Yajra\DataTables\DataTables;


class AgreementAmendmentController extends Controller
{
    private $destinationPath;

    public function __construct(
        protected AgreementAmendmentRepository $amendments
    )
    {
        $this->destinationPath = 'agreement/amendment';
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param mixed $id
     * @return mixed
     *
     */
    public function index(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = $this->amendments
            ->where('mfr_agreement_id', '=', $id)
            ->orderby('created_at', 'desc')
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
                    return $row->approved_budget;
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
                    $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm amendment-form" href="';
                    $btn .= route('mfr.agreement.amendment.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('mfr.agreement.amendment.destroy', $row->id) . '">';
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
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     */
    public function create($id)
    {
        $array = [
            'agreementId' => $id
        ];
        return view('Mfr::Agreements.Amendments.create', $array);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['mfr_agreement_id'] = $id;
        DB::beginTransaction();
        try {
            if ($request->file('attachment')) {
                $filename = time().'_'.random_int(1000, 9999).$request->file('attachment')->getClientOriginalExtension();
                $inputs['attachment'] = $request->file('attachment')->storeAs($this->destinationPath, $filename);
            }
            $inputs['created_by'] = auth()->user()->id;
            $record = $this->amendments->create($inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'amendment' => $record,
                    'extensionDate' => $record->extension_to_date->format('Y-m-d'),
                    'amendmentCount' => $record->agreement->amendments()->count(),
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
        $constructionAmendment = $this->amendments->find($amendmentId);
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
        $amendment = $this->amendments->find($amendmentId);
        $array = [
            'amendment' => $amendment
        ];
        return view('Mfr::Agreements.Amendments.edit', $array);
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
            $record = $this->amendments->update($amendmentId, $inputs);
            DB::commit();
            if ($record) {
                return response()->json([
                    'status' => 'ok',
                    'amendment' => $record,
                                        'extensionDate' => $record->extension_to_date->format('Y-m-d'),
                    'message' => 'Amendment updated successfully.',
                    'amendmentCount' => $record->agreement->amendments()->count()
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
        $agreement = $this->amendments->find($amendmentId)->agreement;
        $deleted = $this->amendments->destroy($amendmentId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Amendment deleted successfully.',
                'amendment' => $agreement->latestAmendment()->exists() ? $agreement->latestAmendment : null,
                'extensionDate' => $agreement->latestAmendment()->exists() ? $agreement->latestAmendment->extension_to_date->format('Y-m-d') : null,
                'amendmentCount' => $agreement->amendments()->count()
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Amendment cannot be deleted.'
            ], 422);
        }
    }
}
