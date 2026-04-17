<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Sector;

class SectorRepository extends Repository
{
    public function __construct(Sector $model)
    {
        $this->model = $model;
    }

    public function getActive()
    {
        return $this->model->where('enable_field', true)->orderBy('title')->get();
    }
}