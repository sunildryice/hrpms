<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\EmployeeHour;

use DB;

class EmployeeHourRepository extends Repository
{
    public function __construct(EmployeeHour $employeeHour)
    {
        $this->model = $employeeHour;
    }
}
