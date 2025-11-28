<?php

namespace Modules\TravelRequest\Repositories;

use DB;
use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelRequestEstimate;


class TravelRequestEstimateRepository extends Repository
{
    public function __construct(TravelRequestEstimate $travelRequestEstimate)
    {
        $this->model = $travelRequestEstimate;
    }
}
