<?php

namespace Modules\TransportationBill\Repositories;

use App\Repositories\Repository;
use Modules\TransportationBill\Models\TransportationBillDetail;

use DB;

class TransportationBillDetailRepository extends Repository
{
    public function __construct(
        TransportationBillDetail $transportationBillDetail
    ){
        $this->model = $transportationBillDetail;
    }
}
