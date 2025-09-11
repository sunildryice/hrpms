<?php

namespace Modules\GoodRequest\Repositories;

use App\Repositories\Repository;
use Modules\GoodRequest\Models\GoodRequestLog;

use DB;

class GoodRequestLogRepository extends Repository
{
    public function __construct(GoodRequestLog $goodRequestLog)
    {
        $this->model = $goodRequestLog;
    }
}
