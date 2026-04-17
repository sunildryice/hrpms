<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Approach;

class ApproachRepository extends Repository
{
    public function __construct(Approach $model)
    {
        $this->model = $model;
    }

    public function getActive()
    {
        return $this->model->where('enable_field', true)->orderBy('title')->get();
    }
}