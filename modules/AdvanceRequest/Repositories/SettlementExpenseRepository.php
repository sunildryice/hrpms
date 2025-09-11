<?php

namespace Modules\AdvanceRequest\Repositories;

use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\SettlementExpense;

use DB;

class SettlementExpenseRepository extends Repository
{
    public function __construct(
        SettlementExpense $settlementExpense
    )
    {
        $this->model = $settlementExpense;
    }


    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $settlementExpense = $this->model->create($inputs);
            DB::commit();
            return $settlementExpense;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateTotalAmount($expenseId)
    {
        DB::beginTransaction();
        try {
            $settlementExpense = $this->model->findOrFail($expenseId);
            $grossAmount = $settlementExpense->details->sum('gross_amount');
            $taxAmount = $settlementExpense->details->sum('tax_amount');
            $netAmount = $settlementExpense->details->sum('net_amount');

            $updateInputs = [
                'gross_amount' => $grossAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
            ];
            $settlementExpense->update($updateInputs);
            DB::commit();
            return $settlementExpense;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
