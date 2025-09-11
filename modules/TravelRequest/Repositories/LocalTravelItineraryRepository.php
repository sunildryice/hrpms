<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\LocalTravelItinerary;

use DB;

class LocalTravelItineraryRepository extends Repository
{
    public function __construct(LocalTravelItinerary $localTravelItinerary)
    {
        $this->model = $localTravelItinerary;
    }
}
