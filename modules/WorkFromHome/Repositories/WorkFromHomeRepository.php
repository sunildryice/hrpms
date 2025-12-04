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
}
