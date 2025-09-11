<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelClaimExpense;

use DB;

class TravelClaimExpenseRepository extends Repository
{
    public function __construct(
        TravelClaimRepository $travelClaims,
        TravelClaimExpense $travelClaimExpense
    )
    {
        $this->travelClaims = $travelClaims;
        $this->model = $travelClaimExpense;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $claimExpense = $this->model->create($inputs);
            $this->travelClaims->updateTotalAmount($claimExpense->travel_claim_id);
            DB::commit();
            return $claimExpense;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $claimExpense = $this->model->findOrFail($id);
            $claimExpense->delete();
            $this->travelClaims->updateTotalAmount($claimExpense->travel_claim_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $claimExpense = $this->model->findOrFail($id);
            $claimExpense->fill($data)->save();
            $this->travelClaims->updateTotalAmount($claimExpense->travel_claim_id);
            DB::commit();
            return $claimExpense;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
