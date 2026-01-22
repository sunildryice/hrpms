<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\ActivityAccessPeriod;

class ActivityAccessPeriodRepository extends Repository
{
    public function __construct(ActivityAccessPeriod $model)
    {
        $this->model = $model;
    }
}
