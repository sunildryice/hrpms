<?php

namespace Modules\TransportationBill\Repositories;

use App\Repositories\Repository;
use Modules\TransportationBill\Models\TransportationBillLog;

use DB;

class TransportationBillLogRepository extends Repository
{
    public function __construct(TransportationBillLog $transportationBillLog)
    {
        $this->model = $transportationBillLog;
    }
}
