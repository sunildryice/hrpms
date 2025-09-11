<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Requests\Designation\StoreRequest;
use Modules\Master\Requests\Designation\UpdateRequest;

use DataTables;

class DesignationController extends Controller
{
    /**
     * The designation repository instance.
     *
     * @var DesignationRepository
     */
    protected $designations;

    /**
     * Create a new controller instance.
     *
     * @param DesignationRepository $designations
     * @return void
     */
    public function __construct(
        DesignationRepository $designations
    )
    {
        $this->designations = $designations;
    }

    /**
     * Display a listing of the designation.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->designations->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-designation-modal-form" href="';
                    $btn .= route('master.designations.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.designations.destroy', $row->id) . '">';
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
        return view('Master::Designation.index')
            ->withDesignations($this->designations->all());
    }

    /**
     * Show the form for creating a new designation.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::Designation.create');
    }

    /**
     * Store a newly created designation in storage.
     *
     * @param \Modules\Master\Requests\Designation\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $designation = $this->designations->create($inputs);
        if ($designation) {
            return response()->json(['status' => 'ok',
                'designation' => $designation,
                'message' => 'Designation is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Designation can not be added.'], 422);
    }

    /**
     * Display the specified designation.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $designation = $this->designations->find($id);
        return response()->json(['status' => 'ok', 'designation' => $designation], 200);
    }

    /**
     * Show the form for editing the specified designation.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $designation = $this->designations->find($id);
        return view('Master::Designation.edit')
            ->withDesignation($designation);
    }

    /**
     * Update the specified designation in storage.
     *
     * @param \Modules\Master\Requests\Designation\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $designation = $this->designations->update($id, $inputs);
        if ($designation) {
            return response()->json(['status' => 'ok',
                'designation' => $designation,
                'message' => 'Designation is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Designation can not be updated.'], 422);
    }

    /**
     * Remove the specified designation from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->designations->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Designation is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Designation can not deleted.',
        ], 422);
    }
}
