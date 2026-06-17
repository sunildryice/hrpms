<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\TravelRequest\Repositories\TravelRequestRepository;

class InvolvedTravelRequestController extends Controller
{
    public function __construct(
        protected TravelRequestRepository $travelRequests
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            if ($authUser->can('view-all-involved-travel-request')) {
                $data = $this->travelRequests->with(['logs', 'travelType', 'status', 'requester.employee', 'employee'])
                    ->whereNotIn('status_id', [config('constant.CREATED_STATUS')])
                    ->orderBy('created_at', 'desc');
            } else {
                $data = $this->travelRequests->with(['logs', 'travelType', 'status', 'requester.employee', 'employee'])
                    ->whereHas('logs', function ($q) use ($authUser) {
                        $q->where('user_id', $authUser->id);
                    })->orderBy('created_at', 'desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->getReturnDate();
                })->addColumn('duration', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('final_destination', function ($row) {
                    return $row->final_destination;
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('travel.requests.view', $row->id) . '" title="View Travel Request"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('TravelRequest::Involved.index');
    }
}
