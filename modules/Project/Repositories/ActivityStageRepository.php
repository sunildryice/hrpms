<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\ActivityStage;

class ActivityStageRepository extends Repository
{
    public function __construct(ActivityStage $model)
    {
        $this->model = $model;
    }
}
