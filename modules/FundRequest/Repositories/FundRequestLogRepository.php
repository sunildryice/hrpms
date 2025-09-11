<?php

namespace Modules\FundRequest\Repositories;

use App\Repositories\Repository;
use Modules\FundRequest\Models\FundRequestLog;

use DB;

class FundRequestLogRepository extends Repository
{
    public function __construct(FundRequestLog $fundRequestLog)
    {
        $this->model = $fundRequestLog;
    }
}
