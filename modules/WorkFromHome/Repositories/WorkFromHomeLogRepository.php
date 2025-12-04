<?php

namespace Modules\WorkFromHome\Repositories;

use App\Repositories\Repository;
use Modules\WorkFromHome\Models\WorkFromHomeLog;

class WorkFromHomeLogRepository extends Repository
{
    public function __construct(protected WorkFromHomeLog $workFromHomeLog)
    {
        $this->model = $workFromHomeLog;
    }
}
