<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TravelRequest\Notifications\LocalTravelPaid;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\Payment\LocalTravel\StoreRequest;
use Yajra\DataTables\DataTables;

class LocalTravelPaymentController extends Controller
{
    private $localTravel;

    private $travelRequest;

    public function __construct(
        LocalTravelRepository $localTravel,
        TravelRequestRepository $travelRequest,

    ) {
        $this->localTravel = $localTravel;
        $this->travelRequest = $travelRequest;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->localTravel->select(['*'])
                ->whereIn('status_id', [config('constant.PAID_STATUS')])
                ->orderBy('local_travel_number', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('paid.local.travel.reimbursements.show', $row->id).'" rel="tooltip" title="View Local Travel Reimbursement">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('local.travel.reimbursements.print', $row->id).'" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::LocalTravel.Paid.index');
    }

    public function show($Id)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravel->find($Id);
        // $travelRequest = isset($localTravel->travel_request_id) ? $this->travelRequest->find($localTravel->travel_request_id) : null;

        return view('TravelRequest::LocalTravel.Paid.show')
            ->with([
                'localTravel' => ($localTravel),
                // 'travelRequest' => ($travelRequest),
                'authUser' => ($authUser),
            ]);
    }

    public function create($id)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravel->find($id);
        $this->authorize('pay', $localTravel);

        return view('TravelRequest::Payment.LocalTravel.create')
            ->withLocalTravel($localTravel);
    }

    public function store(StoreRequest $request, $id)
    {
        $localTravel = $this->localTravel->find($id);
        $this->authorize('pay', $localTravel);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $localTravel = $this->localTravel->pay($id, $inputs);
        if ($localTravel) {
            $localTravel->requester->notify(new LocalTravelPaid($localTravel));

            return response()->json([
                'status' => 'ok',
                'message' => 'Payment is successfully made.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment can not be made.',
        ], 422);
    }
}
