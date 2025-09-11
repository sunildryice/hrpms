<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\AdvanceRequest\Repositories\SettlementExpenseDetailRepository;
use Modules\AdvanceRequest\Requests\SettlementExpense\Detail\StoreRequest;
use Modules\AdvanceRequest\Requests\SettlementExpense\Detail\UpdateRequest;
use Modules\AdvanceRequest\Repositories\SettlementExpenseRepository;
use Modules\Master\Repositories\ExpenseCategoryRepository;
use Modules\Master\Repositories\ExpenseTypeRepository;

class SettlementExpenseDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ExpenseCategoryRepository $expenseCategories ,
     * @param ExpenseTypeRepository $expenseTypes,
     * @param SettlementExpenseRepository $settlementExpenses
     * @param SettlementExpenseDetailRepository $settlementExpenseDetails
     */
    public function __construct(
        ExpenseCategoryRepository       $expenseCategories,
        ExpenseTypeRepository           $expenseTypes,
        SettlementExpenseRepository     $settlementExpenses,
        SettlementExpenseDetailRepository $settlementExpenseDetails
    )
    {
        $this->expenseCategories = $expenseCategories;
        $this->expenseTypes = $expenseTypes;
        $this->settlementExpenses = $settlementExpenses;
        $this->settlementExpenseDetails = $settlementExpenseDetails;
        $this->destinationPath = 'advanceSettlementExpenseDetail';
    }

    /**
     * Show the form for creating a new settlement expenses detail.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $settlementExpense = $this->settlementExpenses->find($id);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $expenseCategories = $this->expenseCategories->get();
        $expenseTypes = $this->expenseTypes->get();

        return view('AdvanceRequest::Settlement.Expense.Detail.create')
            ->withExpenseCategories($expenseCategories)
            ->withExpenseTypes($expenseTypes)
            ->withSettlementExpense($settlementExpense);
    }

    /**
     * Store a newly created settlement expense details in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $settlementExpense = $this->settlementExpenses->find($id);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $inputs = $request->validated();
        $inputs['advance_settlement_id'] = $settlementExpense->advance_settlement_id;
        $inputs['settlement_expense_id'] = $settlementExpense->id;
        $inputs['created_by'] = auth()->id();
        $inputs['net_amount'] = $inputs['gross_amount'] - $inputs['tax_amount'];
        $expenseDetail = $this->settlementExpenseDetails->create($inputs);

        if ($settlementExpense) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath .'/'.$authUser->employee_id, time().'_advance_settlement_expense_detail.'. $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
                $expenseDetail = $this->settlementExpenseDetails->update($expenseDetail->id, $inputs);
            }
            if ($expenseDetail) {
                $attachment = '';
                if (isset($expenseDetail->attachment) && file_exists('storage/'.$expenseDetail->attachment)) {
                    $attachment = '<a href="';
                    $attachment .= asset('storage/' . $expenseDetail->attachment);
                    $attachment .= '" target="_blank" class="fs-5" title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>';
                }
            }
            return response()->json(['status' => 'ok',
                'expenseDetail' => $expenseDetail,
                'settlementExpense' => $expenseDetail->settlementExpense,
                'expenseCategory' => $expenseDetail->getExpenseCategory(),
                'description' => $expenseDetail->getDescription(),
                'expenseDate' => $expenseDetail->getExpenseDate(),
                'expenseType' => $expenseDetail->getExpenseType(),
                'attachment' => $attachment,
                'editUrl' => route('advance.settlement.expense.details.edit', [$expenseDetail->settlement_expense_id, $expenseDetail->id]),
                'deleteUrl'=> route('advance.settlement.expense.details.destroy', [$expenseDetail->settlement_expense_id, $expenseDetail->id]),
                'message' => 'Settlement expense detail is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Settlement expense detail can not be added.'], 422);

    }


    /**
     * Show the form for editing the specified advance settlement expenses.
     *
     * @param int $expenseId
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($expenseId, $id)
    {
        $authUser = auth()->user();
        $settlementExpense = $this->settlementExpenses->find($expenseId);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $expenseDetail = $this->settlementExpenseDetails->find($id);
        $expenseCategories = $this->expenseCategories->get();
        $expenseTypes = $this->expenseTypes->get();

        return view('AdvanceRequest::Settlement.Expense.Detail.edit')
            ->withExpenseCategories($expenseCategories)
            ->withExpenseTypes($expenseTypes)
            ->withExpenseDetail($expenseDetail);
    }


    /**
     * Update the specified settlement expense detail in storage.
     *
     * @param UpdateRequest $request
     * @param $settlementId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $expenseId, $id)
    {
        $authUser = auth()->user();
        $settlementExpense = $this->settlementExpenses->find($expenseId);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['net_amount'] = $inputs['gross_amount'] - $inputs['tax_amount'];
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath .'/'.$authUser->employee_id, time().'_advance_settlement_expense_detail.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $expenseDetail = $this->settlementExpenseDetails->update($id, $inputs);

        if ($expenseDetail) {
            $attachment = '';
            if (isset($expenseDetail->attachment) && file_exists('storage/'.$expenseDetail->attachment)) {
                $attachment = '<a href="';
                $attachment .= asset('storage/' . $expenseDetail->attachment);
                $attachment .= '" target="_blank" class="fs-5" title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>';
            }
            return response()->json([
                'status' => 'ok',
                'expenseDetail' => $expenseDetail,
                'settlementExpense' => $expenseDetail->settlementExpense,
                'expenseCategory' => $expenseDetail->getExpenseCategory(),
                'description' => $expenseDetail->getDescription(),
                'expenseDate' => $expenseDetail->getExpenseDate(),
                'expenseType' => $expenseDetail->getExpenseType(),
                'attachment' => $attachment,
                'editUrl' => route('advance.settlement.expense.details.edit', [$expenseDetail->settlement_expense_id, $expenseDetail->id]),
                'deleteUrl'=> route('advance.settlement.expense.details.destroy', [$expenseDetail->settlement_expense_id, $expenseDetail->id]),
                'message' => 'Settlement expense detail is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Settlement expense detail  can not be updated.'], 422);
    }


    /**
     * Remove the specified Settlement Expense detail from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($expenseId, $id)
    {
        $settlementExpense = $this->settlementExpenses->find($expenseId);
        $this->authorize('update', $settlementExpense->advanceSettlement);
        $expenseDetail = $this->settlementExpenseDetails->find($id);

        $flag = $this->settlementExpenseDetails->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Settlement expense detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Settlement expense detail can not deleted.',
        ], 422);
    }

    public function deleteAttachment($settlementExpenseDetail)
    {
        $settlementExpenseDetail = $this->settlementExpenseDetails->find($settlementExpenseDetail);
        DB::beginTransaction();
        try {
            $settlementExpenseDetail->attachment = null;
            $settlementExpenseDetail->save();
            DB::commit();
            return response()->json([
                'type' => 'success',
                'expenseDetail' => $settlementExpenseDetail,
                'message' => 'Attachment deleted successfully.'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'type' => 'error',
                'message' => 'Attachment could not be successfully.'
            ], 422);
        }
        
    }
}
