<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\TravelRequest\Repositories\TravelClaimRepository;

class InvolvedClaimController extends Controller
{
    public function __construct(
        protected TravelClaimRepository $travelClaim
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            if ($authUser->can('view-all-involved-travel-claim')) {
                $data = $this->travelClaim->with(['travelRequest', 'logs', 'requester', 'status'])
                    ->whereNotIn('status_id', [config('constant.CREATED_STATUS')])
                    ->orderBy('created_at', 'desc');
            } else {
                $data = $this->travelClaim->with(['travelRequest', 'logs', 'requester', 'status'])
                    ->whereHas('logs', function ($q) use ($authUser) {
                        $q->where('user_id', $authUser->id);
                    })->orderBy('created_at', 'desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->travelRequest->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->travelRequest->getReturnDate();
                })->addColumn('final_destination', function ($row) {
                    return $row->travelRequest->final_destination;
                })->addColumn('travel_number', function ($row) {
                    return $row->travelRequest->getTravelRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('travel.claims.view', $row->id) . '" title="View Travel Claim"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelClaim.Involved.index');
    }
}
