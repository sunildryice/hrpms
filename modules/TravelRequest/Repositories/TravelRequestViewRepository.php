<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelRequestView;

class TravelRequestViewRepository extends Repository
{
    public function __construct(
        TravelRequestView $travelRequestView
    )
    {
        $this->model = $travelRequestView;
    }
}
