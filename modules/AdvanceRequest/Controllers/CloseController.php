<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Modules\AdvanceRequest\Notifications\AdvanceRequestClosed;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;

class CloseController extends Controller
{
    protected $advanceRequests;
    /**
     * Create a new controller instance.
     *
 
     * @param AdvanceRequestRepository $advanceRequests
     */
    public function __construct(
       
        AdvanceRequestRepository $advanceRequests,
    )
    {
        $this->advanceRequests = $advanceRequests;
    }

   

    public function create($id)
    {
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('close', $advanceRequest);
        return view('AdvanceRequest::Close.create')
                ->withAdvanceRequest($advanceRequest);
    }


    public function store(Request $request,$prId)
    {
        $inputs = $request->validate([
            'close_remarks' => 'required',
        ]);
        $advanceRequest = $this->advanceRequests->find($prId);
        $this->authorize('close', $advanceRequest);
        $advanceRequest = $this->advanceRequests->close($prId, $inputs);    
        if($advanceRequest){
            $advanceRequest->requester->notify(new AdvanceRequestClosed($advanceRequest));
            return response()->json([
                'type' => 'success',
                'message' => 'Advance Request closed successfully'
            ],200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Advance Request Cannot be Closed',
        ],422);
    }

    
}
