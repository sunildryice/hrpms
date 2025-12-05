<?php

namespace Modules\TravelRequest\Repositories;

use DB;
use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelDsaClaim;

class TravelClaimDsaRepository extends Repository
{
    public function __construct(
        TravelClaimRepository $travelClaims,
        TravelDsaClaim $travelDsaClaim
    )
    {
        $this->travelClaims = $travelClaims;
        $this->model = $travelDsaClaim;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $claimDsa = $this->model->create($inputs);
            $this->travelClaims->updateTotalAmount($claimDsa->travel_claim_id);
            DB::commit();
            return $claimDsa;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $claimDsa = $this->model->findOrFail($id);
            $claimDsa->delete();
            $this->travelClaims->updateTotalAmount($claimDsa->travel_claim_id);
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
            $claimDsa = $this->model->findOrFail($id);
            $claimDsa->fill($data)->save();
            $this->travelClaims->updateTotalAmount($claimDsa->travel_claim_id);
            DB::commit();
            return $claimDsa;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
