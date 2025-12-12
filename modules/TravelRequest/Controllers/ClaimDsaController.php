<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Requests\Claim\DsaClaim\StoreRequest;
use Modules\TravelRequest\Repositories\TravelClaimDsaRepository;
use Modules\TravelRequest\Requests\Claim\DsaClaim\UpdateRequest;

class ClaimDsaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimRepository $travelClaims
     * @param TravelClaimDsaRepository $travelDsaClaims
     * @param TravelModeRepository $travelModes
     */
    public function __construct(
        protected TravelClaimRepository $travelClaims,
        protected TravelClaimDsaRepository $travelDsaClaims,
        protected TravelModeRepository $travelModes,
        protected ActivityCodeRepository $activityCodes,
        protected OfficeRepository $offices
    ) {
        $this->travelClaims = $travelClaims;
        $this->travelDsaClaims = $travelDsaClaims;
        $this->offices = $offices;
        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the travel DSA claims
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelClaimId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelClaim = $this->travelClaims->find($travelClaimId);
            $data = $this->travelDsaClaims->select(['*'])
                ->whereTravelClaimId($travelClaimId)->get();
            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })
                ->addColumn('arrival_date', function ($row) {
                    return $row->getArrivalDate();
                })
                ->addColumn('mode_of_travel', function ($row) {
                    return $row->getTravelModes();
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
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-itinerary-modal-form" href="';
                        $btn .= route('travel.claims.dsa.edit', [$row->travel_claim_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.claims.dsa.destroy', [$row->travel_claim_id, $row->id]) . '">';
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

        return view('TravelRequest::TravelClaim.DsaClaim.create')
            ->withTravelModes($this->travelModes->get())
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
                ->storeAs($this->destinationPath . '/' . $travelClaim->travel_request_id, time() . '_claim_dsa.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['travel_claim_id'] = $travelClaim->id;
        $inputs['created_by'] = $authUser->id;
        $travelDsaClaim = $this->travelDsaClaims->create($inputs);

        if ($travelDsaClaim) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $travelDsaClaim->travelClaim,
                'travelDsaClaim' => $travelDsaClaim,
                'message' => 'Travel DSA Claim Itinerary is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Travel DSA Claim Itinerary can not be added.'
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
        $travelDsaClaim = $this->travelDsaClaims->find($id);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $this->authorize('update', $travelClaim);

        return view('TravelRequest::TravelClaim.DsaClaim.edit')
            ->withTravelModes($this->travelModes->get())
            ->withTravelDsaClaim($travelDsaClaim)
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
        $travelDsaClaims = $this->travelDsaClaims->find($id);
        $this->authorize('update', $travelDsaClaims->travelClaim);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelDsaClaims->travelClaim->travel_request_id, time() . '_claim_dsa.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $travelDsaClaims = $this->travelDsaClaims->update($id, $inputs);
        if ($travelDsaClaims) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $travelDsaClaims->travelClaim,
                'travelDsaClaims' => $travelDsaClaims,
                'message' => 'Travel DSA Claim Itinerary is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Travel DSA Claim Itinerary can not be updated.'
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
        $travelDsaClaims = $this->travelDsaClaims->find($id);
        $this->authorize('delete', $travelDsaClaims->travelClaim);
        $flag = $this->travelDsaClaims->destroy($id);
        if ($flag) {
            $travelClaim = $this->travelClaims->find($claimId);
            return response()->json([
                'type' => 'success',
                'travelClaim' => $travelClaim,
                'message' => 'Travel DSA Claim Itinerary is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'travelClaim' => $travelDsaClaims->travelClaim,
            'message' => 'Travel DSA Claim Itinerary can not deleted.',
        ], 422);
    }
}
