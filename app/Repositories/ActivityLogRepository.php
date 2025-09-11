<?php
namespace App\Repositories;

use App\Models\ActivityLog;

class ActivityLogRepository extends Repository
{
    public function __construct(ActivityLog $log)
    {
        $this->model = $log;
    }
}
