<?php

namespace Modules\TransportationBill\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\TransportationBill\Repositories\TransportationBillDetailRepository;
use Modules\TransportationBill\Repositories\TransportationBillRepository;

use Modules\TransportationBill\Requests\Detail\StoreRequest;
use Modules\TransportationBill\Requests\Detail\UpdateRequest;

use DataTables;

class TransportationBillDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TransportationBillRepository $transportationBills
     * @param TransportationBillDetailRepository $transportationBillDetails
     */
    public function __construct(
        TransportationBillRepository       $transportationBills,
        TransportationBillDetailRepository $transportationBillDetails
    )
    {
        $this->transportationBills = $transportationBills;
        $this->transportationBillDetails = $transportationBillDetails;
    }

    /**
     * Display a listing of the transportation bill details
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $transportationBillId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $transportationBill = $this->transportationBills->find($transportationBillId);
            $data = $this->transportationBillDetails->select([
                'id', 'transportation_bill_id', 'item_description', 'quantity', 'remarks'
            ])->whereTransportationBillId($transportationBillId);
            return  DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($authUser, $transportationBill) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-detail-modal-form" href="';
                    $btn .= route('transportation.bills.details.edit', [$row->transportation_bill_id, $row->id]) . '" rel="tooltip" title="Edit Transportation Bill""><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('transportation.bills.details.destroy', [$row->transportation_bill_id, $row->id]) . '" rel="tooltip" title="Delete Transportation Bill"">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new transportation bill detail.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $transportationBill = $this->transportationBills->find($id);
        return view('TransportationBill::Detail.create')
            ->withTransportationBill($transportationBill);
    }

    /**
     * Store a newly created transportation bill detail in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $transportationBill = $this->transportationBills->find($id);
        $inputs = $request->validated();
        $inputs['transportation_bill_id'] = $transportationBill->id;
        $transportationBillDetail = $this->transportationBillDetails->create($inputs);

        if ($transportationBillDetail) {
            return response()->json(['status' => 'ok',
                'transportationBill' => $transportationBill,
                'transportationBillDetail' => $transportationBillDetail,
                'transportationDetailCount' => $transportationBill->transportationBillDetails->count(),
                'message' => 'Transportation bill detail is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Transportation bill detail can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified transportation bill detail.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($tbId, $id)
    {
        $transportationBill = $this->transportationBills->find($tbId);
        $transportationBillDetail = $this->transportationBillDetails->find($id);
        $this->authorize('update', $transportationBill);

        return view('TransportationBill::Detail.edit')
            ->withTransportationBillDetail($transportationBillDetail);
    }

    /**
     * Update the specified transportation bill detail in storage.
     *
     * @param UpdateRequest $request
     * @param $tbId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $tbId, $id)
    {
        $transportationBillDetail = $this->transportationBillDetails->find($id);
        $this->authorize('update', $transportationBillDetail->transportationBill);
        $inputs = $request->validated();
        $transportationBillDetail = $this->transportationBillDetails->update($id, $inputs);
        if ($transportationBillDetail) {
            return response()->json(['status' => 'ok',
                'transportationBill' => $transportationBillDetail->transportationBill,
                'transportationBillDetail' => $transportationBillDetail,
                'transportationDetailCount' => $transportationBillDetail->transportationBill->transportationBillDetails->count(),
                'message' => 'Transportation bill detail is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Transportation bill detail can not be updated.'], 422);
    }

    /**
     * Remove the specified transportation bill detail from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($tbId, $id)
    {
        $transportationBillDetail = $this->transportationBillDetails->find($id);
        $this->authorize('delete', $transportationBillDetail->transportationBill);
        $flag = $this->transportationBillDetails->destroy($id);
        if ($flag) {
            $transportationBill = $this->transportationBills->find($tbId);
            return response()->json([
                'type' => 'success',
                'transportationBill' => $transportationBill,
                'transportationDetailCount' => $transportationBill->transportationBillDetails->count(),
                'message' => 'Transportation bill detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'transportationBill' => $transportationBillDetail->transportationBill,
            'message' => 'Transportation bill detail can not deleted.',
        ], 422);
    }
}
