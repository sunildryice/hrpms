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
}
