<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\WorkPlanDetail;

class WorkPlanDetailRepository extends Repository
{
    public function __construct(WorkPlanDetail $workPlanDetails)
    {
        $this->model = $workPlanDetails;
    }
}
