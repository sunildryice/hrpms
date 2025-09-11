<?php
namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\TaxDiscount;

class TaxDiscountRepository extends Repository
{
    public function __construct(TaxDiscount $taxDiscount)
    {
        $this->model = $taxDiscount;
    }
}
