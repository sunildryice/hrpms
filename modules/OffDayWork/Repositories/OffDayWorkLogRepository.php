<?php

namespace Modules\OffDayWork\Repositories;


use App\Repositories\Repository;
use Modules\OffDayWork\Models\OffDayWorkLog;

class OffDayWorkLogRepository extends Repository
{
    public function __construct(OffDayWorkLog $offDayWorkLog)
    {
        $this->model = $offDayWorkLog;
    }
}
