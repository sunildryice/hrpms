<?php

namespace Modules\LeaveRequest\Repositories;

use App\Repositories\Repository;
use Modules\LeaveRequest\Models\LeaveRequestLog;

use DB;

class LeaveRequestLogRepository extends Repository
{
    public function __construct(LeaveRequestLog $leaveRequestLog)
    {
        $this->model = $leaveRequestLog;
    }
}
