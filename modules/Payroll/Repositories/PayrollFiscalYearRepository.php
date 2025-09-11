<?php
namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\PayrollFiscalYear;

class PayrollFiscalYearRepository extends Repository
{
    public function __construct(PayrollFiscalYear $payrollFiscalYear)
    {
        $this->model = $payrollFiscalYear;
    }
}
