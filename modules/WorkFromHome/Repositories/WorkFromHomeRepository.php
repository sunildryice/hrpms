<?php

namespace Modules\WorkFromHome\Repositories;

use App\Repositories\Repository;
use Modules\WorkFromHome\Models\WorkFromHome;

class WorkFromHomeRepository extends Repository
{

    public function __construct(protected WorkFromHome $workFromHome)
    {
        $this->model = $workFromHome;
    }


    public function getWorkFromHomeRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'work_from_home_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('work_from_home_number') + 1;
        return $max;
    }
}
