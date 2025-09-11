<?php

namespace Modules\PaymentSheet\Repositories;

use App\Repositories\Repository;
use Modules\PaymentSheet\Models\PaymentBill;

use DB;

class PaymentBillRepository extends Repository
{
    public function __construct(PaymentBill $paymentBill)
    {
        $this->model = $paymentBill;
    }
}
