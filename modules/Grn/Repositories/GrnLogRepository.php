<?php

namespace Modules\Grn\Repositories;

use App\Repositories\Repository;
use Modules\Grn\Models\GrnLog;

use DB;

class GrnLogRepository extends Repository
{
    public function __construct(GrnLog $grnLog)
    {
        $this->model = $grnLog;
    }
}
