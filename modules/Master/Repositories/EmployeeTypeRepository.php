<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\EmployeeType;

class EmployeeTypeRepository extends Repository
{
    public function __construct(EmployeeType $employeeTypes)
    {
        $this->model = $employeeTypes;
    }

    public function getConsultantTypes()
    {
        return $this->model->select(['*'])
            ->where('id', '<>', config('constant.FULL_TIME_EMPLOYEE'))
            ->get();
    }
}
