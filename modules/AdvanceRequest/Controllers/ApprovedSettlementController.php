<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Gate;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementExpenseDetailRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ApprovedSettlementController extends Controller
{
    private $employees;
    private $expenseDetails;
    private $fiscalYears;
    private $advanceRequests;
    private $settlements;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param SettlementRepository $settlements
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository       $employees,
        FiscalYearRepository     $fiscalYears,
        AdvanceRequestRepository $advanceRequests,
        SettlementRepository     $settlements,
        SettlementExpenseDetailRepository $expenseDetails,
        UserRepository           $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->settlements = $settlements;
        $this->expenseDetails = $expenseDetails;
        $this->users = $users;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->settlements->getApproved();
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
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.advance.settlements.show', $row->id) . '" rel="tooltip" title="View Advance Settlement Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('advance.request.settlement.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    if ($authUser->can('pay',$row)){
                        $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                        $btn .= route('approved.settlement.pay.create', $row->id) . '" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('AdvanceRequest::Settlement.Approved.index');
    }

    /**
     * Show the specified advance request settlement in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $advanceSettlementRequest = $this->settlements->with(['status', 'logs.createdBy'])->find($id);
        $this->authorize('print', $advanceSettlementRequest);

        $expenseSummary = $this->expenseDetails->with(['expenseCategory', 'expenseType'])
                ->where('advance_settlement_id', $id)
                ->select(['expense_type_id'])
                ->selectRaw("SUM(gross_amount) as total_gross_amount")
                ->selectRaw("SUM(tax_amount) as total_tax_amount")
                ->selectRaw("SUM(net_amount) as total_net_amount")
                ->groupBy('expense_type_id')
                ->get();

        return view('AdvanceRequest::Settlement.print')
            ->withSettlement($advanceSettlementRequest)
            ->withExpenseSummary($expenseSummary);
    }

    /**
     * Show the specified advance request.
     *
     * @param $advanceSettlementRequestId
     * @return mixed
     */
    public function show($advanceSettlementRequestId)
    {
        $authUser = auth()->user();
        $advanceSettlementRequest = $this->settlements->find($advanceSettlementRequestId);
        $this->authorize('viewApproved', $advanceSettlementRequest->advanceRequest);
        return view('AdvanceRequest::Settlement.Approved.show')
            ->withAdvanceRequest($advanceSettlementRequest->advanceRequest)
            ->withAdvanceSettlementRequest($advanceSettlementRequest);
    }
}
