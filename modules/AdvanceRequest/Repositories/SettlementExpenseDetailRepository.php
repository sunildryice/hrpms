<?php

namespace Modules\AdvanceRequest\Repositories;

use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\SettlementExpenseDetail;

use DB;

class SettlementExpenseDetailRepository extends Repository
{
    public function __construct(
        SettlementExpenseDetail $settlementExpenseDetail,
        SettlementExpenseRepository $settlementExpenses
    )
    {
        $this->model = $settlementExpenseDetail;
        $this->settlementExpenses = $settlementExpenses;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $settlementExpenseDetail = $this->model->create($inputs);
            $this->settlementExpenses->updateTotalAmount($settlementExpenseDetail->settlement_expense_id);
            DB::commit();
            return $settlementExpenseDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $settlementExpenseDetail = $this->model->findOrFail($id);
            $settlementExpenseDetail->delete();
            $this->settlementExpenses->updateTotalAmount($settlementExpenseDetail->settlement_expense_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlementExpenseDetail = $this->model->findOrFail($id);
            $settlementExpenseDetail->fill($inputs)->save();
            $this->settlementExpenses->updateTotalAmount($settlementExpenseDetail->settlement_expense_id);
            DB::commit();
            return $settlementExpenseDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
