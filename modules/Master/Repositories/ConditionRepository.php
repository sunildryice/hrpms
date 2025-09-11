<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Condition;

class ConditionRepository extends Repository
{
    public function __construct(
        Condition $condition
    )
    {
        $this->model = $condition;
    }

    public function getConditions()
    {
        return $this->model->whereNotNull('activated_at')->get();
    }
}