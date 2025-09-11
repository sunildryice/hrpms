<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Execution;

class ExecutionRepository extends Repository
{
    public function __construct(
        Execution $execution
    )
    {
        $this->model = $execution;
    }

    public function getExecutions()
    {
        return $this->model->whereNotNull('activated_at')->get();
    }
}