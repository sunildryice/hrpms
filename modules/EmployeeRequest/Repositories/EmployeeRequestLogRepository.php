<?php

namespace Modules\EmployeeRequest\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeRequest\Models\EmployeeRequestLog;

use DB;

class EmployeeRequestLogRepository extends Repository
{
    public function __construct(EmployeeRequestLog $employeeRequestLog)
    {
        $this->model = $employeeRequestLog;
    }
}
