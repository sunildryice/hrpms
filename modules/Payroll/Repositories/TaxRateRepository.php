<?php
namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\TaxRate;

class TaxRateRepository extends Repository
{
    public function __construct(TaxRate $taxRate)
    {
        $this->model = $taxRate;
    }
}
