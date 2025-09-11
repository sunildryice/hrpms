<?php

namespace Modules\DistributionRequest\Repositories;

use App\Repositories\Repository;
use Modules\DistributionRequest\Models\DistributionHandoverItem;
use DB;

class DistributionHandoverItemRepository extends Repository
{
    public function __construct(
        DistributionHandoverItem $distributionHandoverItem
    )
    {
        $this->model = $distributionHandoverItem;
    }
}
