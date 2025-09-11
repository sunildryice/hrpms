<?php

namespace Modules\VehicleRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Modules\VehicleRequest\Notifications\VehicleRequestClosed;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;




class CloseController extends Controller
{
    /**
     * Create a new controller instance.
     * @param VehicleRequestRepository $vehicleRequests
     */
    public function __construct(
       
        VehicleRequestRepository $vehicleRequests,
    )
    {
        $this->vehicleRequests = $vehicleRequests;
    }

    

    public function create($vehicleRequestId)
    {
       $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('close', $vehicleRequest);
        return view('VehicleRequest::Close.create')
                ->withVehicleRequest($vehicleRequest); 
    }

    public function store(Request $request, $vehicleRequestId)
    {
        $inputs = $request->validate([
            'close_remarks' => 'required',
        ]);
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('close', $vehicleRequest);
        $vehicleRequest = $this->vehicleRequests->close($vehicleRequestId, $inputs);
        if($vehicleRequest){
            $vehicleRequest->requester->notify(new VehicleRequestClosed($vehicleRequest));
            return response()->json([
                'type' => 'success',
                'message' => 'Vehicle request closed successfully'
            ],200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Vehicle Request Cannot be Closed',
        ],422);
    }
}
