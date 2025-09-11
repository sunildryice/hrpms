<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ProbationaryIndicatorRepository;
use Modules\Master\Requests\ProbationaryIndicator\StoreRequest;
use Modules\Master\Requests\ProbationaryIndicator\UpdateRequest;

use DataTables;

class ProbationaryIndicatorController extends Controller
{
    /**
     * The probationary indicator repository instance.
     *
     * @var ProbationaryIndicatorRepository
     */
    protected $probationaryIndicators;

    /**
     * Create a new controller instance.
     *
     * @param ProbationaryIndicatorRepository $probationaryIndicators
     * @return void
     */
    public function __construct(
        ProbationaryIndicatorRepository $probationaryIndicators
    )
    {
        $this->probationaryIndicators = $probationaryIndicators;
    }

    /**
     * Display a listing of the probationary indicator.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->probationaryIndicators->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-indicator-modal-form" href="';
                    $btn .= route('master.probationary.indicators.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.probationary.indicators.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::ProbationaryIndicator.index');
    }

    /**
     * Show the form for creating a new probationary indicator.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ProbationaryIndicator.create');
    }

    /**
     * Store a newly created probationary indicator in storage.
     *
     * @param \Modules\Master\Requests\ProbationaryIndicator\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $probationaryIndicator = $this->probationaryIndicators->create($inputs);
        if ($probationaryIndicator) {
            return response()->json(['status' => 'ok',
                'probationaryIndicator' => $probationaryIndicator,
                'message' => 'Probationary indicator is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Probationary indicator can not be added.'], 422);
    }

    /**
     * Display the specified probationary indicator.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $probationaryIndicator = $this->probationaryIndicators->find($id);
        return response()->json(['status' => 'ok', 'probationaryIndicator' => $probationaryIndicator], 200);
    }

    /**
     * Show the form for editing the specified probationary indicator.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $probationaryIndicator = $this->probationaryIndicators->find($id);
        return view('Master::ProbationaryIndicator.edit')
            ->withProbationaryIndicator($probationaryIndicator);
    }

    /**
     * Update the specified probationary indicator in storage.
     *
     * @param \Modules\Master\Requests\ProbationaryIndicator\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $probationaryIndicator = $this->probationaryIndicators->update($id, $inputs);
        if ($probationaryIndicator) {
            return response()->json(['status' => 'ok',
                'probationaryIndicator' => $probationaryIndicator,
                'message' => 'Probationary indicator is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Probationary indicator can not be updated.'], 422);
    }

    /**
     * Remove the specified probationary indicator from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->probationaryIndicators->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Probationary indicator is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Probationary indicator can not deleted.',
        ], 422);
    }
}
