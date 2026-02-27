<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestViewRepository;
use Yajra\DataTables\DataTables;

class TravelRequestApprovedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelRequestRepository $travelRequest
     * @param TravelRequestViewRepository $travelRequestView
     */
    public function __construct(
        TravelRequestRepository     $travelRequest,
        TravelRequestViewRepository $travelRequestView,
    )
    {
        $this->travelRequest = $travelRequest;
        $this->travelRequestView = $travelRequestView;
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
            $query = $this->travelRequestView->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
            if ($authUser->employee->office->office_type_id == config('constant.HEAD_OFFICE')) {
                $query->where(function ($q) use ($accessibleOfficeIds) {
                    $q->whereNull('office_id');
                    $q->orWhereIn('office_id', $accessibleOfficeIds);
                });
            } else {
                $query->whereIn('office_id', $accessibleOfficeIds);
            }
            $data = $query->orderBy('departure_date', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->getReturnDate();
                })->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->status_class . '">' . $row->status_title . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.travel.requests.show', $row->id) . '" rel="tooltip" title="View Travel Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('travel.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelRequestApproved.index');
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function ticketIndex(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->travelRequestView->select(['*'])
                ->where(function ($query) {
                    $query->where('air_ticket_count', '>', 0)
                        ->orWhere('vehicle_count', '>', 0);
                })->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                ->orderBy('departure_date', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->getReturnDate();
                })->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.travel.requests.show', $row->id) . '" rel="tooltip" title="View Travel Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('travel.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelRequestApproved.ticketIndex');
    }

    /**
     * Show the specified advance request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $travelRequest = $this->travelRequest->find($id);

        return view('TravelRequest::TravelRequestApproved.print')
            ->withRequester($travelRequest->requester->employee)
            ->withTravelRequest($travelRequest);
    }

    /**
     * Show the specified travel request.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($id);

        return view('TravelRequest::TravelRequestApproved.show')
            ->withRequester($travelRequest->requester->employee)
            ->withTravelRequest($travelRequest);
    }
}
