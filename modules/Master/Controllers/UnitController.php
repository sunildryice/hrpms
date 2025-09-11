<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\UnitRepository;
use Modules\Master\Requests\Unit\StoreRequest;
use Modules\Master\Requests\Unit\UpdateRequest;

use DataTables;

class UnitController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UnitRepository $units
     * @return void
     */
    public function __construct(
        UnitRepository $units
    )
    {
        $this->units = $units;
    }

    /**
     * Display a listing of the unit.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->units->select([
                'id', 'title', 'activated_at', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-unit-modal-form" href="';
                    $btn .= route('master.units.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.units.destroy', $row->id) . '">';
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
        return view('Master::Unit.index');
    }

    /**
     * Show the form for creating a new unit.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::Unit.create');
    }

    /**
     * Store a newly created unit in storage.
     *
     * @param \Modules\Master\Requests\Unit\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $unit = $this->units->create($inputs);
        if ($unit) {
            return response()->json(['status' => 'ok',
                'unit' => $unit,
                'message' => 'Unit is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Unit can not be added.'], 422);
    }

    /**
     * Display the specified unit.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = $this->units->find($id);
        return response()->json(['status' => 'ok', 'unit' => $unit], 200);
    }

    /**
     * Show the form for editing the specified unit.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $unit = $this->units->find($id);
        return view('Master::Unit.edit')
            ->withUnit($unit);
    }

    /**
     * Update the specified unit in storage.
     *
     * @param \Modules\Master\Requests\Unit\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $unit = $this->units->update($id, $inputs);
        if ($unit) {
            return response()->json(['status' => 'ok',
                'unit' => $unit,
                'message' => 'Unit is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Unit can not be updated.'], 422);
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->units->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Unit is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Unit can not deleted.',
        ], 422);
    }
}
