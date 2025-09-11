<?php

namespace Modules\TravelAuthorization\Repositories;

use App\Repositories\Repository;
use Modules\TravelAuthorization\Models\TravelAuthorizationEstimate;

class TravelAuthorizationEstimateRepository extends Repository
{
    public function __construct(TravelAuthorizationEstimate $travel)
    {
        $this->model = $travel;
    }
}
