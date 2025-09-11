<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Modules\Master\Repositories\OfficeRepository;
use Modules\TravelRequest\Repositories\TravelClaimItineraryRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Requests\Claim\Itinerary\UpdateRequest;

use DB;
use DataTables;

class ClaimItineraryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimItineraryRepository $travelClaimItineraries
     * @param TravelClaimRepository $travelClaims
     */
    public function __construct(
        TravelClaimItineraryRepository $travelClaimItineraries,
        TravelClaimRepository          $travelClaims,
        OfficeRepository $offices
    )
    {
        $this->travelClaimItineraries = $travelClaimItineraries;
        $this->travelClaims = $travelClaims;
        $this->offices = $offices;
        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the travel request itineraries
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelClaimId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelClaim = $this->travelClaims->find($travelClaimId);
            $data = $this->travelClaimItineraries->with(['travelRequestItinerary'])
                ->whereTravelClaimId($travelClaimId)
                ->get();

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->travelRequestItinerary->getDepartureDate();
                })->addColumn('departure_place', function ($row) {
                    return $row->travelRequestItinerary->departure_place;
                })->addColumn('arrival_date', function ($row) {
                    return $row->travelRequestItinerary->getArrivalDate();
                })->addColumn('arrival_place', function ($row) {
                    return $row->travelRequestItinerary->arrival_place;
                })->addColumn('overnights', function ($row) {
                    return $row->travelRequestItinerary->getOvernights();
                })->addColumn('dsa_unit_price', function ($row) {
                    return $row->travelRequestItinerary->dsa_unit_price;
                })->addColumn('activity_code', function ($row) {
                    return $row->travelRequestItinerary->activityCode->getActivityCode();
                })->addColumn('charging_office', function ($row) {
                    return $row->office->getOfficeName();
                })->addColumn('attachment', function ($row) {
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
                        $btn .= route('travel.claims.itineraries.edit', [$row->travel_claim_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                });

            return $datatable->rawColumns(['action', 'attachment'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($travelClaimId, $id)
    {
        $travelClaimItinerary = $this->travelClaimItineraries->find($id);
        $this->authorize('update', $travelClaimItinerary->travelClaim);
        return view('TravelRequest::TravelClaim.Itinerary.edit')
            ->withOffices($this->offices->getActiveOffices())
            ->withTravelClaimItinerary($travelClaimItinerary);
    }

    /**
     * Update the specified itinerary in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $travelClaimId, $id)
    {
        $travelClaimItinerary = $this->travelClaimItineraries->find($id);
        $this->authorize('update', $travelClaimItinerary->travelClaim);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelClaimItinerary->travelClaim->travel_request_id, time() . '_claim_itinerary.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $inputs['dsa_rate'] = $travelClaimItinerary->travelRequestItinerary->dsa_unit_price;
        $inputs['overnights'] = $travelClaimItinerary->travelRequestItinerary->getOvernights();
        $dsaAmount = $inputs['overnights'] * $inputs['dsa_rate'];
        $dsaAmount = $inputs['overnights'] > 0 ? $dsaAmount : $inputs['dsa_rate'];
        $inputs['total_amount'] = $dsaAmount ? $dsaAmount * $inputs['percentage_charged'] / 100 : 0;

        $travelClaimItinerary = $this->travelClaimItineraries->update($id, $inputs);
        if ($travelClaimItinerary) {
            return response()->json(['status' => 'ok',
                'travelClaimItinerary' => $travelClaimItinerary,
                'travelClaim' => $travelClaimItinerary->travelClaim,
                'message' => 'Travel Request Itinerary is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Travel Request Itinerary can not be updated.'], 422);
    }
}
