<?php

namespace Modules\PurchaseOrder\Repositories;

use App\Repositories\Repository;
use Modules\PurchaseOrder\Models\PurchaseOrderLog;

use DB;

class PurchaseOrderLogRepository extends Repository
{
    public function __construct(PurchaseOrderLog $purchaseOrderLog)
    {
        $this->model = $purchaseOrderLog;
    }
}
