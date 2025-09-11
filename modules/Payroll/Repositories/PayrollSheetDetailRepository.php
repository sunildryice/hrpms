<?php

namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\PayrollSheetDetail;

class PayrollSheetDetailRepository extends Repository
{
    /**
     * @param PayrollSheetDetail $payrollSheetDetails
     */
    public function __construct(
        PayrollSheetDetail      $payrollSheetDetails
    )
    {
        $this->model = $payrollSheetDetails;
    }
}
