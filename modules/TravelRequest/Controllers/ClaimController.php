<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Notifications\TravelClaimSubmitted;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\Claim\UpdateRequest;

class ClaimController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected TravelClaimRepository $travelClaim,
        protected TravelRequestRepository $travelRequest,
        protected TravelRequestEstimateRepository $travelRequestEstimate,
        protected TravelRequestItineraryRepository $travelRequestItinerary,
        protected RoleRepository $roles,
        protected StatusRepository $status,
        protected UserRepository $user
    ) {}

    /**
     * Display a listing of the travel claims by employee id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelClaim->with(['travelRequest', 'logs', 'requester'])
                ->whereCreatedBy($authUser->id)
                // ->orWhereHas('logs', function ($q) use ($authUser) {
                //     $q->where('user_id', $authUser->id);
                //     $q->orWhere('original_user_id', $authUser->id);
                // })
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
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('travel.claims.view', $row->id).'" rel="tooltip" title="View Travel Claim"><i class="bi-eye"></i></a>&emsp;';
                    if ($authUser->can('update', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('travel.claims.edit', $row->id).'" rel="tooltip" title="Edit Travel Claim"><i class="bi-pencil-square"></i></a>&emsp;';
                        if ($authUser->can('delete', $row)) {
                            $btn .= '<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                            $btn .= 'data-href="'.route('travel.claims.destroy', $row->id).'"  rel="tooltip" title="Delete Travel Claim">';
                            $btn .= '<i class="bi-trash3"></i></a>';
                        }
                    } else {
                        if ($authUser->can('print', $row)) {
                            $btn .= '<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                            $btn .= route('travel.claim.print', $row->id).'" rel="tooltip" title="Print Travel Claim"><i class="bi bi-printer"></i></a>';
                        }
                    }

                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelClaim.index');
    }

    /**
     * Store a newly created travel claim in storage.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, $travelRequestId)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('createClaim', $travelRequest);
        $travelClaim = $this->travelClaim->createClaim($travelRequestId, $authUser->id);
        if ($travelClaim) {
            return response()->json(['status' => 'ok',
                'travelClaim' => $travelClaim,
                'redirectUrl' => route('travel.claims.edit', $travelClaim->id),
                'message' => 'Travel claim is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Travel claim can not be updated.'], 422);
    }

    /**
     * Show the form for editing a travel report by user.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($claimId)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($claimId);
        $this->authorize('update', $travelClaim);
        $reviewers = $this->user->permissionBasedUsers('finance-review-travel-claim');
        $approvers = $this->user->permissionBasedUsers('approve-travel-claim');
        // $approvers = $this->user->getSupervisors($authUser);

        return view('TravelRequest::TravelClaim.edit')
            ->withAuthUser($authUser)
            ->withReviewers($reviewers)
            ->withApprovers($approvers)
            ->withTravelClaim($travelClaim)
            ->withTravelRequest($travelClaim->travelRequest);
    }

    /**
     * Store a newly created travel report in storage.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $travelClaimId)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($travelClaimId);
        $this->authorize('update', $travelClaim);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $requester = $travelClaim->requester;
        $latestTenure = $requester->employee->latestTenure;
        $supervisor = $this->user->select('id')
            ->whereIn('employee_id', [$latestTenure->supervisor_id])
            ->first();
        $inputs['agree_at'] = $request->agree ? date('Y-m-d H:i:s') : null;
        // $inputs['approver_id'] = $inputs['recommender_id'] = $supervisor->id;
        $inputs['recommender_id'] = $inputs['approver_id'];
        $travelClaim = $this->travelClaim->update($travelClaim->id, $inputs);
        if ($travelClaim) {
            $message = 'Travel claim is successfully updated.';
            if ($travelClaim->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Travel claim is successfully submitted.';
                $travelClaim->reviewer->notify(new TravelClaimSubmitted($travelClaim));
            } elseif (in_array($travelClaim->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])) {
                return redirect()->route('travel.claims.edit', $travelClaim->id)
                    ->withSuccessMessage($message);
            }

            return redirect()->route('travel.claims.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Travel claim can not be updated.');
    }

    /**
     * View the details the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($id);
        $travelRequest = $this->travelRequest->find($travelClaim->travel_request_id);

        return view('TravelRequest::TravelClaim.view')
            ->withAuthUser($authUser)
            ->withTravelClaim($travelClaim)
            ->withTravelRequest($travelRequest);
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $travelClaim = $this->travelClaim->find($id);
        $this->authorize('delete', $travelClaim);
        $flag = $this->travelClaim->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel Request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel Request can not deleted.',
        ], 422);
    }

    /**
     * Show the specified travel claim in printable view
     *
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($id);
        $this->authorize('print', $travelClaim);
        // $itineraries = $travelClaim->itineraries()->with(['travelRequestItinerary.activityCode', 'travelRequestItinerary.donorCode', 'office'])
        //     ->select(['travel_claim_itineraries.*', 'i2.activity_code_id'])
        //     ->join('travel_request_itineraries as i2', 'i2.id', '=', 'travel_claim_itineraries.travel_itinerary_id')
        //     ->get();

        // $itineraries = $itineraries->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
        //     ->map(function ($itinerary) {
        //         $dsa = collect();
        //         foreach ($itinerary as $index => $value) {
        //             if ($index == 0) {
        //                 $dsa = $value;

        //                 continue;
        //             }
        //             $dsa->total_amount += $value->total_amount;
        //         }
        //         $dsa->subledger = 'DSA';

        //         return $dsa;
        //     });
        // $expenses = $travelClaim->expenses()->with(['activityCode', 'donorCode', 'office'])->get();

        // $expenses = $expenses->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
        //     ->map(function ($expense) {
        //         $travel = collect();
        //         foreach ($expense as $index => $value) {
        //             if ($index == 0) {
        //                 $travel = $value;

        //                 continue;
        //             }
        //             $travel->expense_amount += $value->expense_amount;
        //         }
        //         $travel->subledger = 'Travel';

        //         return $travel;
        //     });
        // $summaries = $expenses->merge($itineraries)->groupBy('activity_code_id')->flatten(1);

        return view('TravelRequest::TravelClaim.print')
            // ->withSummaries($summaries)
            ->withTravelClaim($travelClaim);
    }
}
