<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\PartnerOrganizationRepository;
use Modules\Master\Requests\PartnerOrganization\StoreRequest;
use Modules\Master\Requests\PartnerOrganization\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class PartnerOrganizationController extends Controller
{
    public function __construct(
        protected PartnerOrganizationRepository $partners,
        protected DistrictRepository $districts
    ) {
    }

    /**
     * Display a listing of the project code.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->partners->select([
                'id', 'name', 'district_id']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-project-modal-form" href="';
                    $btn .= route('master.partner.org.edit', $row->id).'"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('master.partner.org.destroy', $row->id).'">';
                    $btn .= '<i class="bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::PartnerOrganization.index');
    }

    /**
     * Show the form for creating a new project code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::PartnerOrganization.create')->with('districts', $this->districts->getEnabledDistricts());
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $partnerOrganization = $this->partners->create($inputs);
        if ($partnerOrganization) {
            return response()->json(['status' => 'ok',
                'project code' => $partnerOrganization,
                'message' => 'Partner Organization is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Partner Organization can not be added.'], 422);
    }

    public function show($id)
    {
        $partnerOrganization = $this->partners->find($id);

        return response()->json(['status' => 'ok', 'projectCode' => $partnerOrganization], 200);
    }

    public function edit($id)
    {
        $partnerOrganization = $this->partners->find($id);

        return view('Master::PartnerOrganization.edit')
            ->with('partner', $partnerOrganization)
            ->with('districts', $this->districts->getEnabledDistricts());
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $partnerOrganization = $this->partners->update($id, $inputs);
        if ($partnerOrganization) {
            return response()->json(['status' => 'ok',
                'partnerOrg' => $partnerOrganization,
                'message' => 'Partner Organization is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Partner Organization can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->partners->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Partner Organization is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Partner Organization can not deleted.',
        ], 422);
    }
}
