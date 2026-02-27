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
    ) {
        $this->travelClaims = $travelClaims;
        $this->model = $travelDsaClaim;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $claimDsa = $this->model->create($inputs);
            if (array_key_exists('travel_modes', $inputs)) {
                $claimDsa->travelModes()->sync($inputs['travel_modes']);
            }
            $this->travelClaims->updateTotalAmount($claimDsa->travel_claim_id);
            DB::commit();
            return $claimDsa;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $claimDsa = $this->model->findOrFail($id);
            $claimDsa->fill($inputs)->save();
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
            $claimDsa->travelModes()->detach();
            $claimDsa->delete();
            $this->travelClaims->updateTotalAmount($claimDsa->travel_claim_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
