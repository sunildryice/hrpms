<?php

namespace Modules\TravelAuthorization\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelAuthorization\Requests\Official\StoreRequest;
use Modules\TravelAuthorization\Requests\Official\UpdateRequest;

use DataTables;
use Modules\TravelAuthorization\Models\TravelAuthorization;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationOfficialRepository;

class TravelAuthorizationOfficialController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected TravelAuthorization $travel,
        protected TravelAuthorizationOfficialRepository $travelOfficials,
        protected RoleRepository $roles,
        protected UserRepository $user,
        protected DistrictRepository $districts
    ) {
        $this->destinationPath = 'travelAuthorization';
    }

    public function index(Request $request, $taId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travel = $this->travel->find($taId);
            $data = $this->travelOfficials->select(['*'])
                ->with('district')
                ->where('travel_authorization_id', $taId);

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function ($row) {
                    return $row->district->district_name;
                });
            if ($authUser->can('update', $travel)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $travel) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-official-modal-form" href="';
                    $btn .= route('ta.requests.official.edit', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Edit Travel Cost Estimation"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('ta.requests.official.destroy', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Delete Travel Cost Estimation">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    public function create($taId)
    {
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        return view('TravelAuthorization::Official.create')
            ->withDistricts($this->districts->getDistricts())
            ->withTravel($travel);
    }

    public function store(StoreRequest $request, $taId)
    {
        $inputs = $request->validated();
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        $inputs['travel_authorization_id'] = $travel->id;
        $inputs['created_by'] = auth()->id();
        $official = $this->travelOfficials->create($inputs);
        if ($official) {
            return response()->json([
                'status' => 'ok',
                'officialsCount' => $official->travelAuthorization->officials()->count(),
                'message' => 'Officials is successfully created.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Officials can not be updated.'
        ], 422);
    }

    public function edit($taId, $id)
    {
        $official = $this->travelOfficials->find($id);
        $this->authorize('update', $official->travelAuthorization);
        return view('TravelAuthorization::Official.edit')
            ->withTravel($official->travelAuthorization)
            ->withOfficial($official)
            ->withDistricts($this->districts->getDistricts());
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $official = $this->travelOfficials->find($id);
        $this->authorize('update', $official->travelAuthorization);
        $inputs['updated_by'] = auth()->id();
        $official = $this->travelOfficials->update($id, $inputs);
        if ($official) {
            return response()->json([
                'status' => 'ok',
                'officialsCount' => $official->travelAuthorization->officials()->count(),
                'message' => 'Officials is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Officials can not be updated.'
        ], 422);
    }

    public function destroy($taId, $id)
    {
        $official = $this->travelOfficials->find($id);
        $this->authorize('delete', $official->travelAuthorization);
        $flag = $this->travelOfficials->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'officialsCount' => $official->travelAuthorization->officials()->count(),
                'message' => 'Travel Official is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel Official can not deleted.',
        ], 422);
    }
}
