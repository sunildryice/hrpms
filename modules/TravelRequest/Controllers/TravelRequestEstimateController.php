<?php

namespace Modules\TravelRequest\Controllers;

use DB;
use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Requests\TravelRequestEstimate\StoreRequest;

class TravelRequestEstimateController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository      $employees,
     * @param TravelRequestRepository $travelRequest,
     * @param TravelRequestEstimateRepository $travelRequestEstimate,
     * @param TravelRequestItineraryRepository $travelRequestItinerary,
     * @param RoleRepository          $roles,
     * @param UserRepository          $user
     *
     */
    public function __construct(
        EmployeeRepository      $employees,
        TravelRequestRepository $travelRequest,
        TravelRequestEstimateRepository $travelRequestEstimate,
        TravelRequestItineraryRepository $travelRequestItinerary,
        RoleRepository          $roles,
        UserRepository          $user
    )
    {
        $this->employees        = $employees;
        $this->travelRequest    = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->roles            = $roles;
        $this->user             = $user;
        $this->destinationPath  = 'travelrequest';
    }

    /**
     * Display a listing of the travel request estimates
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelRequest = $this->travelRequest->find($travelRequestId);
            $data = $this->travelRequestEstimate->select(['*'])
                ->whereTravelRequestId($travelRequestId);

            $datatable = DataTables::of($data)
                ->addIndexColumn();
                
            if ($authUser->can('update', $travelRequest)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $travelRequest) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-estimation-modal-form" href="';
                    $btn .= route('travel.requests.estimate.edit', [$row->travel_request_id, $row->id]) . '" rel="tooltip" title="Edit Travel Cost Estimation"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('travel.requests.estimate.destroy', [$row->travel_request_id, $row->id]) . '" rel="tooltip" title="Delete Travel Cost Estimation">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new travel request estimate.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($travelRequestId)
    {
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('update', $travelRequest);
        $estimatedDsaAmount = $travelRequest->travelRequestItineraries->sum('dsa_total_price');
        return view('TravelRequest::TravelRequestEstimate.create')
            ->withEstimatedDsaAmount($estimatedDsaAmount)
            ->withTravelRequest($travelRequest);
    }

    /**
     * Create new or update(if already exist) travel request estimate in storage.
     *
     * @param StoreRequest $request
     * @param $travelRequestId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $travelRequestId)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('update', $travelRequest);
        $inputs['travel_request_id'] = $travelRequest->id;
        $inputs['created_by'] = auth()->id();
        $travelRequestEstimate = $this->travelRequestEstimate->updateOrCreate(['travel_request_id'=>$travelRequestId], $inputs);
        if($travelRequestEstimate){
            $estimate = $this->travelRequestEstimate->find($travelRequestEstimate->id);
            return response()->json(['status' => 'ok',
                'travelRequestEstimate' => $estimate,
                'estimateCount'=>$travelRequestEstimate->travelRequest->travelRequestEstimate()->count(),
                'message' => 'Travel Request Estimate is successfully added.'], 200);
        }
        return response()->json(['status'=>'error',
            'message'=>'Travel Request Estimate can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified travel request estimate.
     *
     * @param int $travelRequestId
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($travelRequestId, $id)
    {
        $travelRequestEstimate = $this->travelRequestEstimate->find($id);
        $this->authorize('update', $travelRequestEstimate->travelRequest);
        return view('TravelRequest::TravelRequestEstimate.edit')
            ->withTravelRequestEstimate($travelRequestEstimate);
    }

    /**
     * Remove the specified estimate from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($travelReqyestId, $id)
    {
        $travelRequestEstimate = $this->travelRequestEstimate->find($id);
        $this->authorize('delete', $travelRequestEstimate->travelRequest);
        $flag = $this->travelRequestEstimate->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'estimateCount'=>$travelRequestEstimate->travelRequest->travelRequestEstimate()->count(),
                'message' => 'Travel request estimate is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel request estimate can not deleted.',
        ], 422);
    }
}
