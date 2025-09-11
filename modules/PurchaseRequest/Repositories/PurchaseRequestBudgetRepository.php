<?php

namespace Modules\PurchaseRequest\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\FiscalYear;
use Modules\PurchaseRequest\Models\PurchaseRequest;

use Modules\PurchaseRequest\Models\PurchaseRequestBudget;

class PurchaseRequestBudgetRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        FiscalYear $fiscalYears,
        PurchaseRequest $purchaseRequest,
        PurchaseRequestBudget $purchaseRequestBudgets
    )
    {
        $this->fiscalYears = $fiscalYears;
        $this->model = $purchaseRequestBudgets;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequestBudget = $this->model->create($inputs);
            DB::commit();
            return $purchaseRequestBudget;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $purchaseRequestBudget = $this->model->findOrFail($id);
            $purchaseRequestBudget->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequestBudget = $this->model->find($id);
            $purchaseRequestBudget->fill($inputs)->save();
            DB::commit();
            return $purchaseRequestBudget;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
