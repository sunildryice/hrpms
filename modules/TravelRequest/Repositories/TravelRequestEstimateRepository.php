<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelRequestEstimate;

use DB;

class TravelRequestEstimateRepository extends Repository
{
    public function __construct(TravelRequestEstimate $travelRequestEstimate)
    {
        $this->model = $travelRequestEstimate;
    }
}
