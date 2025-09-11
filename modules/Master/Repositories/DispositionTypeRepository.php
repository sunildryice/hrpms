<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\DispositionType;

class DispositionTypeRepository extends Repository
{
    public function __construct(DispositionType $dispositionType)
    {
        $this->model = $dispositionType;
    }

    public function getDispositionTypes()
    {
        return $this->model->all();
    }
}
