<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Department;

class DepartmentRepository extends Repository
{
    public function __construct(Department $department)
    {
        $this->model = $department;
    }

    public function getActiveDepartments()
    {
        return $this->model->select(['*'])
            ->orderBy('title')->get();
    }
}
