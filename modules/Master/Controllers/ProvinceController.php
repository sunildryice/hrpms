<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Master\Requests\Province\UpdateRequest;

class ProvinceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  ProvinceRepository $provinces
     * @return void
     */
    public function __construct(
        ProvinceRepository $provinces
    )
    {
        $this->provinces = $provinces;
    }

    /**
     * Display a listing of the province.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->provinces->select([
                'id', 'province_name', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-province-modal-form" href="';
                    $btn .= route('master.provinces.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
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

        return view('Master::Province.index');
    }

    /**
     * Show the form for editing the specified province.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $province = $this->provinces->find($id);
        return view('Master::Province.edit')
            ->withProvince($province);
    }

    /**
     * Update the specified province in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $province = $this->provinces->update($id, $inputs);
        if ($province) {
            return response()->json(['status' => 'ok',
                'province' => $province,
                'message' => 'Province is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Province can not be updated.'], 422);
    }
}
