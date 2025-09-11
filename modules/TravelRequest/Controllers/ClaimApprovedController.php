<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;

use DataTables;


class ClaimApprovedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimRepository $travelClaims
     * @param UserRepository $users
     */
    public function __construct(
        TravelClaimRepository     $travelClaims,
        UserRepository $users
    )
    {
        $this->travelClaims = $travelClaims;
        $this->users = $users;
    }

    /**
     * Display a listing of the travel claims
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelClaims->with([
                'travelRequest' => function ($q) {
                    $q->with(['fiscalYear'])
                        ->select(['id', 'departure_date', 'return_date','final_destination', 'prefix', 'travel_number', 'modification_number','fiscal_year_id']);
                },
                 'requester',
                  'status'
                  ])->select(['*'])
                ->whereIn('status_id',[config('constant.APPROVED_STATUS'),config('constant.PAID_STATUS')])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->travelRequest->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->travelRequest->getReturnDate();
                })->addColumn('final_destination', function ($row) {
                    return $row->travelRequest->final_destination;
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('travel_number', function ($row) {
                    return $row->travelRequest->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('travel.claims.view', $row->id) . '" rel="tooltip" title="View Travel Claim">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('approved.travel.claims.print', $row->id) . '" rel="tooltip" title="Print Travel Claim"><i class="bi bi-printer"></i></a>';
                    if ($authUser->can('pay',$row)){
                        $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                        $btn .= route('approved.travel.claims.pay.create', $row->id) . '" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelClaim.Approved.index');
    }

    /**
     * Show the specified travel claim in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($id);
        $this->authorize('printApproved', $travelClaim);

        $itineraries = $travelClaim->itineraries()->with(['travelRequestItinerary.activityCode', 'travelRequestItinerary.donorCode', 'office'])
        ->select(['travel_claim_itineraries.*', 'i2.activity_code_id'])
        ->join('travel_request_itineraries as i2','i2.id','=', 'travel_claim_itineraries.travel_itinerary_id')
        ->get();

        $itineraries = $itineraries->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
        ->map(function($itinerary){
            $dsa = collect();
            foreach($itinerary as $index => $value){
                if($index == 0){
                    $dsa = $value;
                    continue;
                }
                $dsa->total_amount += $value->total_amount;
            }
            $dsa->subledger = 'DSA';
            return $dsa;
        });
        $expenses = $travelClaim->expenses()->with(['activityCode', 'donorCode', 'office'])->get();
        
        $expenses = $expenses->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
            ->map(function($expense){
                $travel = collect();
                foreach($expense as $index => $value){
                    if($index == 0){
                        $travel = $value;
                        continue;
                    }
                    $travel->expense_amount += $value->expense_amount;
                }
                $travel->subledger = 'Travel';
            return $travel;
        });
        $summaries = $expenses->merge($itineraries)->groupBy('activity_code_id')->flatten(1);


        return view('TravelRequest::TravelClaim.print')
            ->withSummaries($summaries)
            ->withTravelClaim($travelClaim);
    }

}
