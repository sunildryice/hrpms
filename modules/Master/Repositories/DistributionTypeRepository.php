<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\DistributionType;

class DistributionTypeRepository extends Repository
{
    public function __construct(DistributionType $distributionType)
    {
        $this->model = $distributionType;
    }
}
