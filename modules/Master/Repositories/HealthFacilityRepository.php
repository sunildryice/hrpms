<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\HealthFacility;

class HealthFacilityRepository extends Repository
{
    public function __construct(
        HealthFacility $healthFacility
    )
    {
        $this->model = $healthFacility;
    }

    public function getHealthFacilities()
    {
        return $this->model->get();
    }
}