<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\ActivityUpdatePeriod;

class ActivityUpdatePeriodRepository extends Repository
{
    public function __construct(ActivityUpdatePeriod $model)
    {
        $this->model = $model;
    }
}
