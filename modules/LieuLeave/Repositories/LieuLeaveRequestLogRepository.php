<?php

namespace Modules\LieuLeave\Repositories;


use App\Repositories\Repository;
use Modules\LieuLeave\Models\LieuLeaveRequestLog;

class LieuLeaveRequestLogRepository extends Repository
{
    public function __construct(LieuLeaveRequestLog $lieuLeaveRequestLog)
    {
        $this->model = $lieuLeaveRequestLog;
    }
}
