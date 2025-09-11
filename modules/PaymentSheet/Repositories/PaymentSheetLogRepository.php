<?php

namespace Modules\PaymentSheet\Repositories;

use App\Repositories\Repository;
use Modules\PaymentSheet\Models\PaymentSheetLog;

use DB;

class PaymentSheetLogRepository extends Repository
{
    public function __construct(PaymentSheetLog $paymentSheetLog)
    {
        $this->model = $paymentSheetLog;
    }
}
