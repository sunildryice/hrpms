<?php
namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\PaymentItem;

class PaymentItemRepository extends Repository
{
    public function __construct(PaymentItem $paymentItem)
    {
        $this->model = $paymentItem;
    }
}
