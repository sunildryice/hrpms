<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\ActivityUpdatePeriod;

class ActivityStageRepository extends Repository
{
    public function __construct(ActivityStage $model)
    {
        $this->model = $model;
    }

    public function checkCurrentActivityAccess(): bool
    {
        $currentDate = date('Y-m-d');

        return ActivityUpdatePeriod::active()
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->exists();
    }
}
