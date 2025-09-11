<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Controller;

use Modules\AdvanceRequest\Requests\Payment\StoreRequest;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementPaid;

class SettlementPaymentController extends Controller
{
    private $employees;
    private $expenseDetails;
    private $fiscalYears;
    private $advanceRequests;
    private $settlements;
    private $users;

    public function __construct(
        SettlementRepository     $settlements,

    )
    {
        $this->settlements = $settlements;

    }

    public function index(Request $request){
        $authUser = auth()->user();
        if($request->ajax()){
            $data = $this->advances->getPaid();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('advance_number', function ($row) {
                    return $row->advanceRequest->getAdvanceRequestNumber();
                })->addColumn('request-date', function ($row) {
                    return $row->advanceRequest->getRequestDate();
                })->addColumn('expense_amount', function ($row) {
                    return $row->getSettlementExpenseAmount();
                })->addColumn('completion_date', function ($row) {
                    return $row->getCompletionDate();
                })->addColumn('status',function($row){
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('paid.advance.settlement.show', $row->id) . '" rel="tooltip" title="View  Payment Sheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('advance.request.settlement.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('AdvanceRequest::Settlement.Paid.index');
    }

    public function create($id){
        $authUser = auth()->user();
        $settlement = $this->settlements->find($id);
        $this->authorize('pay',$settlement);

        return view('AdvanceRequest::Payment.Settlement.create')
                ->withAdvancedSettlementRequest($settlement);
    }

    public function store(StoreRequest $request, $id){
        $inputs = $request->validated();
        $settlement = $this->settlements->find($id);
        $this->authorize('pay',$settlement);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $settlement = $this->settlements->pay($id,$inputs);
        if($settlement){
            $settlement->requester->notify(new AdvanceSettlementPaid($settlement));
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

    public function show($id)
    {
        $advanceSettlementRequest = $this->settlements->find($id);
        $this->authorize('viewApproved', $advanceSettlementRequest);
        return view('AdvanceRequest::Settlement.Paid.show')
            ->withAdvanceRequest($advanceSettlementRequest->advanceRequest)
            ->withAdvanceSettlementRequest($advanceSettlementRequest);
    }
}






