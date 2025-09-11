<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Master\Requests\District\UpdateRequest;

class DistrictController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  DistrictRepository $districts
     * @param  ProvinceRepository $provinces
     * @return void
     */
    public function __construct(
        DistrictRepository $districts,
        ProvinceRepository $provinces
    )
    {
        $this->districts = $districts;
        $this->provinces = $provinces;
    }

    /**
     * Display a listing of the district.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->districts->select(['*'])->with(['province']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-district-modal-form" href="';
                    $btn .= route('master.districts.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    return $btn;
                })->addColumn('province', function ($row) {
                    return $row->getProvinceName();
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->addColumn('enabled', function ($row){
                    return $row->getEnableField();
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::District.index');
    }

    /**
     * Show the form for editing the specified district.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $district = $this->districts->find($id);
        return view('Master::District.edit')
            ->withDistrict($district)
            ->withProvinces($this->provinces->get());
    }

    /**
     * Update the specified district in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['enable_field'] = $request->enable_field ? 1: 0;
        $district = $this->districts->update($id, $inputs);
        if ($district) {
            return response()->json(['status' => 'ok',
                'district' => $district,
                'message' => 'District is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'District can not be updated.'], 422);
    }
}
