<?php

namespace Modules\AdvanceRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\AdvanceRequest\Repositories\SettlementExpenseDetailRepository;
use Modules\AdvanceRequest\Repositories\SettlementExpenseRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\AdvanceRequest\Requests\SettlementExpense\StoreRequest;
use Modules\AdvanceRequest\Requests\SettlementExpense\UpdateRequest;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\DonorCodeRepository;

class SettlementExpenseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DistrictRepository $districts,
        protected SettlementExpenseDetailRepository $expenseDetails,
        protected SettlementExpenseRepository $settlementExpenses,
        protected SettlementRepository $settlements,
        protected AccountCodeRepository $accountCodes,
        protected ActivityCodeRepository $activityCodes,
        protected DonorCodeRepository $donorCodes
    ) {
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $advanceSettlementId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $advanceRequest = $this->settlements->find($advanceSettlementId);
            $data = $this->settlementExpenses->select(['*'])
                ->where('advance_settlement_id', '=', $advanceSettlementId);
            $datatable = Datatables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $advanceRequest)) {
                $datatable->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-expense-modal-form" rel="tooltip" title="Edit Expense" href="';
                    $btn .= route('advance.settlement.expense.edit', [$row->advance_settlement_id, $row->id]).'"><i class="bi-pencil"></i></a>';

                    //                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    //                    $btn .= 'data-href="' . route('advance.settlement.expense.destroy', [$row->advance_settlement_id, $row->id]) . '">';
                    //                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }

            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivityCode();
            })->addColumn('expenseCategory', function ($row) {
                return $row->getexpenseCategory();
            })->addColumn('expenseType', function ($row) {
                return $row->getexpenseType();
            })->rawColumns(['action'])
                ->make(true);
        }

        return true;
    }

    /**
     * Show the form for creating a new advance settlement expenses.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $settlement = $this->settlements->find($id);
        $this->authorize('update', $settlement);
        $advanceDetails = $settlement->advanceRequest->advanceRequestDetails;
        $settlement = $this->settlements->find($id);
        $districts = $this->districts->getEnabledDistricts();

        return view('AdvanceRequest::Settlement.Expense.create')
            ->withAdvanceDetails($advanceDetails)
            ->withDistricts($districts)
            ->withSettlement($settlement);
    }

    /**
     * Store a newly created advance settlement expenses in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $settlement = $this->settlements->find($id);
        $this->authorize('update', $settlement);
        $inputs = $request->validated();
        $inputs['advance_settlement_id'] = $id;
        $inputs['created_by'] = auth()->id();
        $settlementExpense = $this->settlementExpenses->create($inputs);

        if ($settlementExpense) {
            return response()->json(['status' => 'ok',
                'settlementExpenses' => $settlementExpense,
                'message' => 'Settlement expense is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Settlement expense can not be added.'], 422);

    }

    /**
     * Show the form for editing the specified advance settlement expenses.
     *
     * @param  int  $settlementId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($settlementId, $id)
    {
        $authUser = auth()->user();
        $settlement = $this->settlements->find($settlementId);

        $this->authorize('update', $settlement);
        $settlementExpense = $this->settlementExpenses->find($id);
        $districts = $this->districts->getEnabledDistricts();
        $accountCodes = $settlementExpense->activityCode ? $settlementExpense->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('AdvanceRequest::Settlement.Expense.edit', compact('accountCodes', 'activityCodes', 'donorCodes') )
            ->withAdvanceSettlement($settlement)
            ->withDistricts($districts)
            ->withSettlementExpense($settlementExpense);
    }

    /**
     * Update the specified advance settlement expense in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $settlementId, $id)
    {
        $settlementExpense = $this->settlementExpenses->find($id);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $settlementExpense = $this->settlementExpenses->update($id, $inputs);

        if ($settlementExpense) {
            return response()->json(['status' => 'ok',
                'district' => $settlementExpense->getDistrictName(),
                'settlementExpense' => $settlementExpense,
                'settlementExpenseCount' => $settlementExpense->advanceSettlement->settlementExpenses()->count(),
                'message' => 'Settlement Expenses  is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Settlement Expenses  can not be updated.'], 422);
    }

    /**
     * Remove the specified Settlement Expense from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($settlementId, $id)
    {
        $settlement = $this->settlements->find($settlementId);
        $settlementExpense = $this->settlementExpenses->find($id);
        $this->authorize('update', $settlement);

        $flag = $this->settlementExpenses->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Settlement expense is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Settlement expense can not deleted.',
        ], 422);
    }

    public function summary(Request $request, $advanceSettlementId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $settlement = $this->settlements->find($advanceSettlementId);
            $data = $this->expenseDetails->with(['expenseCategory', 'expenseType'])
                ->where('advance_settlement_id', $advanceSettlementId)
                // ->select(['expense_category_id'])
                ->select(['expense_type_id'])
                ->selectRaw('SUM(gross_amount) as total_gross_amount')
                ->selectRaw('SUM(tax_amount) as total_tax_amount')
                ->selectRaw('SUM(net_amount) as total_net_amount')
                ->groupBy('expense_type_id')
                ->get();

            $datatable = Datatables::of($data)
                ->addIndexColumn();

            return $datatable
                // ->addColumn('expenseCategory', function ($row) {
                //     return $row->getExpenseCategory();
                // })
                ->addColumn('expenseType', function ($row) {
                    return $row->getExpenseType();
                })
                ->addColumn('gross_amount', function ($row) {
                    return $row->total_gross_amount;
                })->addColumn('tax_amount', function ($row) {
                    return $row->total_tax_amount;
                })->addColumn('net_amount', function ($row) {
                    return $row->total_net_amount;
                })->rawColumns(['action'])
                ->make(true);
        }

        return true;
    }
}
