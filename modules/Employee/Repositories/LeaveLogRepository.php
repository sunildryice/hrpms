<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\LeaveLog;

class LeaveLogRepository extends Repository
{
    public function __construct(LeaveLog $leaveLog)
    {
        $this->model = $leaveLog;
    }
}
