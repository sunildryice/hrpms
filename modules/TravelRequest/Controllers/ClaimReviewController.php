<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\TravelRequest\Notifications\TravelClaimReturned;
use Modules\TravelRequest\Notifications\TravelClaimForwarded;
use Modules\TravelRequest\Repositories\TravelClaimRepository;

use Modules\TravelRequest\Requests\Claim\Review\StoreRequest;

use DataTables;


class ClaimReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimRepository $travelClaims
     */
    public function __construct(
        protected TravelClaimRepository     $travelClaims
    )
    {
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
            $data = $this->travelClaims->with(['travelRequest', 'requester'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

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
                    $btn .= route('review.travel.claims.create', $row->id) . '" rel="tooltip" title="Review Travel Claim">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelClaim.Review.index');
    }

    public function create($travelClaimId)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($travelClaimId);
        $this->authorize('review', $travelClaim);

        return view('TravelRequest::TravelClaim.Review.create')
            ->withAuthUser($authUser)
            ->withTravelClaim($travelClaim)
            ->withTravelRequest($travelClaim->travelRequest);
    }

    public function store(StoreRequest $request, $travelClaimId)
    {
        $travelClaim = $this->travelClaims->find($travelClaimId);
        $this->authorize('review', $travelClaim);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelClaim = $this->travelClaims->review($travelClaim->id, $inputs);

        if ($travelClaim) {
            $message = '';
            if ($travelClaim->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Travel claim is successfully returned.';
                $travelClaim->requester->notify(new TravelClaimReturned($travelClaim));
            } else {
                $message = 'Travel claim is successfully forwarded for approval.';
                $travelClaim->approver->notify(new TravelClaimForwarded($travelClaim));
            }

            return redirect()->route('review.travel.claims.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel claim can not be reviewed.');
    }
}
