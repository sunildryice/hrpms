<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Notifications\TravelClaimApproved;
use Modules\TravelRequest\Notifications\TravelClaimReturned;
use Modules\TravelRequest\Notifications\TravelClaimForwarded;
use Modules\TravelRequest\Repositories\TravelClaimRepository;

use Modules\TravelRequest\Requests\Claim\Approve\StoreRequest;

use DataTables;


class ClaimApproveController extends Controller
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
            $data = $this->travelClaims->with(['travelRequest', 'requester'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('recommender_id', $authUser->id);
                    $q->where('status_id', config('constant.VERIFIED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED2_STATUS'));
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
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.travel.claims.create', $row->id) . '" rel="tooltip" title="Approve Travel Claim">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelClaim.Approve.index');
    }

    public function create($travelClaimId)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($travelClaimId);
        $this->authorize('approve', $travelClaim);

        $latestTenure = $travelClaim->requester->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name'])
            ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        return view('TravelRequest::TravelClaim.Approve.create')
            ->withAuthUser($authUser)
            ->withSupervisors($supervisors)
            ->withTravelClaim($travelClaim)
            ->withTravelRequest($travelClaim->travelRequest);
    }

    public function store(StoreRequest $request, $travelClaimId)
    {
        $travelClaim = $this->travelClaims->find($travelClaimId);
        $this->authorize('approve', $travelClaim);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelClaim = $this->travelClaims->approve($travelClaim->id, $inputs);

        if ($travelClaim) {
            $message = '';
            if ($travelClaim->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Travel claim is successfully returned.';
                $travelClaim->requester->notify(new TravelClaimReturned($travelClaim));
            } else if ($travelClaim->status_id == config('constant.RECOMMENDED2_STATUS')) {
                $message = 'Travel claim is successfully recommended.';
                $travelClaim->approver->notify(new TravelClaimForwarded($travelClaim));
            } else {
                $message = 'Travel claim is successfully approved.';
                $travelClaim->requester->notify(new TravelClaimApproved($travelClaim));
            }

            return redirect()->route('approve.travel.claims.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel claim can not be approved.');
    }
}
