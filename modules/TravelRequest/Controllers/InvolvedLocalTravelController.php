<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\TravelRequest\Repositories\LocalTravelRepository;

class InvolvedLocalTravelController extends Controller
{
    public function __construct(
        protected LocalTravelRepository $localTravels
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->localTravels->with(['logs', 'status', 'requester', 'travelRequest'])
                ->whereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                })->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })->addColumn('title', function ($row) {
                    return $row->title;
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('local.travel.reimbursements.show', $row->id) . '" title="View Local Travel"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::LocalTravel.Involved.index');
    }
}
