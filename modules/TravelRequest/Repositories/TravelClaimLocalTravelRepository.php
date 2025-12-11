<?php

namespace Modules\TravelRequest\Repositories;

use DB;
use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelClaimLocalTravel;

class TravelClaimLocalTravelRepository extends Repository
{
    public function __construct(
        protected TravelClaimRepository $travelClaims,
        protected TravelClaimLocalTravel $travelClaimLocalTravel
    ) {
        $this->travelClaims = $travelClaims;
        $this->model = $travelClaimLocalTravel;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaimLocalTravel = $this->model->create($inputs);
            $this->travelClaims->updateTotalAmount($travelClaimLocalTravel->travel_claim_id);
            DB::commit();
            return $travelClaimLocalTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaimLocalTravel = $this->model->findOrFail($id);
            $travelClaimLocalTravel->fill($inputs)->save();
            $this->travelClaims->updateTotalAmount($travelClaimLocalTravel->travel_claim_id);
            DB::commit();
            return $travelClaimLocalTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $travelClaimLocalTravel = $this->model->findOrFail($id);
            $travelClaimLocalTravel->delete();
            $this->travelClaims->updateTotalAmount($travelClaimLocalTravel->travel_claim_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
