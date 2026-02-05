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

    public function checkCurrentActivePeriod(): bool
    {
        $currentDate = date('Y-m-d');

        return $this->model->active()
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->exists();
    }
}
