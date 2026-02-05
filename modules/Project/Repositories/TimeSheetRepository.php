<?php

// repo for project activity timesheet
namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Project\Models\TimeSheet;
use Illuminate\Database\QueryException;

class TimeSheetRepository extends Repository
{
    public function __construct(TimeSheet $model)
    {
        $this->model = $model;
    }

    public function getQuery()
    {
        return $this->model;
    }
}
