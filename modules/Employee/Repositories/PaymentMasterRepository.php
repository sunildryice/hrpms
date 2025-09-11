<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\PaymentMaster;

class PaymentMasterRepository extends Repository
{
    public function __construct(PaymentMaster $paymentMaster)
    {
        $this->model = $paymentMaster;
    }
}
