<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\OfficeType;
use Modules\Master\Repositories\OfficeTypeRepository;
use Modules\Master\Requests\OfficeType\StoreOfficeTypeRequest;
use Modules\Master\Requests\OfficeType\UpdateOfficeTypeRequest;
use Yajra\DataTables\DataTables;

class OfficeTypeController extends Controller
{
    private $officeTypes; 

    public function __construct(
        OfficeTypeRepository $officeTypes
    )
    {
        $this->officeTypes = $officeTypes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->officeTypes->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-office-type-modal-form" href="';
                    $btn .= route('master.office.types.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.office.types.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('title', function ($row) {
                    return $row->getTitle();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::OfficeType.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Master::OfficeType.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Modules\Master\Requests\OfficeType\StoreOfficeTypeRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOfficeTypeRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $officeType = $this->officeTypes->create($inputs);

        if ($officeType) {
            return response()->json(['status' => 'ok',
                'officeType' => $officeType,
                'message' => 'Office type is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Office type can not be added.'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Modules\Master\Models\OfficeType  $officeType
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(OfficeType $officeType)
    {
        $officeType = $this->officeTypes->find($officeType);
        return response()->json(['status' => 'ok', 'officeType' => $officeType], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Modules\Master\Models\OfficeType  $officeType
     * @return \Illuminate\Http\Response
     */
    public function edit($officeType)
    {
        $officeType = $this->officeTypes->find($officeType);
        return view('Master::OfficeType.edit')
            ->withOfficeType($officeType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Modules\Master\Requests\OfficeType\UpdateOfficeTypeRequest  $request
     * @param  \Modules\Master\Models\OfficeType  $officeType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOfficeTypeRequest $request, $officeType)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $officeType = $this->officeTypes->update($officeType, $inputs);
        if ($officeType) {
            return response()->json(['status' => 'ok',
                'officeType' => $officeType,
                'message' => 'Office type is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Office type can not be updated.'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Modules\Master\Models\OfficeType  $officeType
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($officeType)
    {
        $flag = $this->officeTypes->destroy($officeType);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Office type is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Office type can not be deleted.',
        ], 422);
    }
}
