<?php

namespace Modules\ExitStaffClearance\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;

class StaffClearanceDepartmentRepository extends Repository
{
    public function __construct(StaffClearanceDepartment $staffClearanceKeyGoal)
    {
        $this->model = $staffClearanceKeyGoal;
    }

    public function getParentDepartments()
    {
        return $this->model->with('childrens')->where('parent_id', 0)->get();
    }

}
