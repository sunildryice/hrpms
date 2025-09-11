<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

use Modules\TravelRequest\Requests\Payment\TravelClaim\StoreRequest;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Notifications\TravelClaimPaid;
use Modules\TravelRequest\Repositories\TravelRequestRepository;



class ClaimPaymentController extends Controller
{
    private $travelClaim;
    private $travelRequest;
    public function __construct(
        TravelClaimRepository     $travelClaim,
        TravelRequestRepository              $travelRequest,
   
    )
    {
        $this->travelClaim = $travelClaim;
        $this->travelRequest = $travelRequest;
    }

    public function index(Request $request){
        $authUser = auth()->user();
        if($request->ajax()){
            $data = $this->travelClaim->with(['travelRequest', 'requester'])->select(['*'])
            ->whereIn('status_id',[config('constant.PAID_STATUS')])->get();
            
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
                    $btn .= route('paid.travel.claims.show', $row->id) . '" rel="tooltip" title="View  Payment Sheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.travel.claims.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('TravelRequest::TravelClaim.Paid.index');
    }

    public function show($Id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($Id);
        $travelRequest = $this->travelRequest->find($travelClaim->travel_request_id);
        // $this->authorize('viewApproved', $travelClaim);
        return view('TravelRequest::TravelClaim.Paid.show')
            ->withTravelClaim($travelClaim)
            ->withTravelRequest($travelRequest)
            ->withAuthUser($authUser);
    }

    public function create($id){
        $authUser = auth()->user();
        $travelClaim = $this->travelClaim->find($id);
        $this->authorize('pay', $travelClaim);
        return view('TravelRequest::Payment.TravelClaim.create')
                ->withtravelClaim($travelClaim);
    }

    public function store(StoreRequest $request, $id){
        $travelClaim = $this->travelClaim->find($id);
        $this->authorize('pay', $travelClaim);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $travelClaim = $this->travelClaim->pay($id,$inputs);
        if($travelClaim){
            $travelClaim->requester->notify(new TravelClaimPaid($travelClaim));
            return response()->json([
                'status' => 'ok',
                'message' => 'Payment is successfully made.'
            ],200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Payment can not be made.'
        ],422);
    }

    }

  

  

    
