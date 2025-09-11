<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Modules\PurchaseRequest\Notifications\PurchaseRequestClosed;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;

class CloseController extends Controller
{
    /**
     * Create a new controller instance.
     *
 
     * @param PurchaseRequestRepository $purchaseRequests
     */
    public function __construct(
        protected PurchaseRequestRepository $purchaseRequests,
    ) {
    }



    public function create($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('close', $purchaseRequest);
        return view('PurchaseRequest::Close.create')
            ->withPurchaseRequest($purchaseRequest);
    }


    public function store(Request $request, $prId)
    {
        $inputs = $request->validate([
            'close_remarks' => 'required',
        ]);
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $this->authorize('close', $purchaseRequest);
        $purchaseRequest = $this->purchaseRequests->close($prId, $inputs);
        if ($purchaseRequest) {
            $purchaseRequest->requester->notify(new PurchaseRequestClosed($purchaseRequest));
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase request closed successfully'
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase Request Cannot be Closed',
        ], 422);
    }

    /**
     * Open closed purchase requests
     * 
     * @param Request $request
     * @param $id Purcase Request Id
     */
    public function open(Request $request, $id)
    {
        $inputs = $request->validate([
            'open_remarks' => 'required|string'
        ]);
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('open', $purchaseRequest);
        $purchaseRequest = $this->purchaseRequests->open($purchaseRequest->id, $inputs);
        if ($purchaseRequest) {
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase request opened successfully'
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase Request Cannot be Opened',
        ], 422);
    }
}
