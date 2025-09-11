<?php

namespace Modules\LeaveRequest\Repositories;

use App\Repositories\Repository;
use Modules\LeaveRequest\Models\LeaveEncashLog;

class LeaveEncashLogRepository extends Repository
{
    public function __construct(LeaveEncashLog $leaveEncashLog)
    {
        $this->model = $leaveEncashLog;
    }
}
