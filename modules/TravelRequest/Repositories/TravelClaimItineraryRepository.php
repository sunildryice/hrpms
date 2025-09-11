<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelClaimItinerary;

use DB;

class TravelClaimItineraryRepository extends Repository
{
    public function __construct(
        TravelClaimRepository $travelClaims,
        TravelClaimItinerary $travelClaimItinerary
    )
    {
        $this->travelClaims = $travelClaims;
        $this->model = $travelClaimItinerary;
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $claimItinerary = $this->model->findOrFail($id);
            $claimItinerary->fill($data)->save();
            $this->travelClaims->updateTotalAmount($claimItinerary->travel_claim_id);
            DB::commit();
            return $claimItinerary;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
