<?php

namespace Modules\DistributionRequest\Repositories;

use App\Repositories\Repository;
use Modules\DistributionRequest\Models\DistributionRequestLog;

use DB;

class DistributionRequestLogRepository extends Repository
{
    public function __construct(DistributionRequestLog $fundRequestLog)
    {
        $this->model = $fundRequestLog;
    }
}
