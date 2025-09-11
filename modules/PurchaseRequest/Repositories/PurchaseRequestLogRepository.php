<?php

namespace Modules\PurchaseRequest\Repositories;

use App\Repositories\Repository;
use Modules\PurchaseRequest\Models\PurchaseRequestLog;

use DB;

class PurchaseRequestLogRepository extends Repository
{
    public function __construct(PurchaseRequestLog $purchaseRequestLog)
    {
        $this->model = $purchaseRequestLog;
    }
}
