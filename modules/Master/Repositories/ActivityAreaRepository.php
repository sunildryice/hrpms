<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ActivityArea;

class ActivityAreaRepository extends Repository
{
    public function __construct(ActivityArea $activityArea)
    {
        $this->model = $activityArea;
    }
}
