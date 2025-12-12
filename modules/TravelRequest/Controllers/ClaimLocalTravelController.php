<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Requests\Claim\LocalTravelClaim\StoreRequest;
use Modules\TravelRequest\Repositories\TravelClaimLocalTravelRepository;
use Modules\TravelRequest\Requests\Claim\LocalTravelClaim\UpdateRequest;

class ClaimLocalTravelController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimRepository $travelClaims
     * @param TravelClaimLocalTravelRepository $localTravels
     * @param TravelModeRepository $travelModes
     */
    public function __construct(
        protected TravelClaimRepository $travelClaims,
        protected TravelClaimLocalTravelRepository $localTravels,
        protected TravelModeRepository $travelModes,
        protected ActivityCodeRepository $activityCodes,
        protected OfficeRepository $offices
    ) {
        $this->travelClaims = $travelClaims;
        $this->localTravels = $localTravels;
        $this->offices = $offices;
        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the local travel claims
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelClaimId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelClaim = $this->travelClaims->find($travelClaimId);
            $data = $this->localTravels->select(['*'])->with(['activityCode'])
                ->whereTravelClaimId($travelClaimId)->get();
            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('travel_date', function ($row) {
                    return $row->getTravelDate();
                })
                ->addColumn('activity', function ($row) {
                    return $row->activityCode?->getActivityCodeDescription();
                })
                ->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ';
                        $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }
                    return $attachment;
                })->addColumn('action', function ($row) use ($authUser, $travelClaim) {
                    $btn = '';
                    if ($authUser->can('update', $travelClaim)) {
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-local-travel-modal-form" href="';
                        $btn .= route('travel.claims.local.travel.edit', [$row->travel_claim_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.claims.local.travel.destroy', [$row->travel_claim_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                });
            return $datatable->rawColumns(['action', 'attachment'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new travel claim DSA.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $travelClaim = $this->travelClaims->find($id);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();

        return view('TravelRequest::TravelClaim.LocalTravelClaim.create')
            ->withActivityCodes($activityCodes)
            ->withTravelClaim($travelClaim);
    }

    /**
     * Store a newly created travel claim DSA in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($id);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelClaim->travel_request_id, time() . '_local_travel_claim.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['travel_claim_id'] = $travelClaim->id;
        $inputs['created_by'] = $authUser->id;
        $localTravel = $this->localTravels->create($inputs);

        if ($localTravel) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $localTravel->travelClaim,
                'localTravel' => $localTravel,
                'message' => 'Local Travel Claim is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Local Travel Claim can not be added.'
        ], 422);
    }

    /**
     * Show the form for editing the specified travel claim DSA.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($claimId, $id)
    {
        $travelClaim = $this->travelClaims->find($claimId);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $localTravel = $this->localTravels->find($id);
        $this->authorize('update', $travelClaim);

        return view('TravelRequest::TravelClaim.LocalTravelClaim.edit')
            ->withLocalTravelClaim($localTravel)
            ->withActivityCodes($activityCodes)
            ->withTravelClaim($travelClaim);
    }

    /**
     * Update the specified travel claim DSA in storage.
     *
     * @param UpdateRequest $request
     * @param $claimId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $claimId, $id)
    {
        $localTravels = $this->localTravels->find($id);
        $this->authorize('update', $localTravels->travelClaim);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $localTravels->travelClaim->travel_request_id, time() . '_local_travel_claim.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $localTravels = $this->localTravels->update($id, $inputs);
        if ($localTravels) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $localTravels->travelClaim,
                'localTravels' => $localTravels,
                'message' => 'Local Travel Claim is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Local Travel Claim can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified travel claim DSA from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($claimId, $id)
    {
        $localTravels = $this->localTravels->find($id);
        $this->authorize('delete', $localTravels->travelClaim);
        $flag = $this->localTravels->destroy($id);
        if ($flag) {
            $travelClaim = $this->travelClaims->find($claimId);
            return response()->json([
                'type' => 'success',
                'travelClaim' => $travelClaim,
                'message' => 'Local Travel Claim is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'travelClaim' => $localTravels->travelClaim,
            'message' => 'Local Travel Claim can not deleted.',
        ], 422);
    }
}
