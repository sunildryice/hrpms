<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Unit;

class UnitRepository extends Repository
{
    public function __construct(Unit $unit)
    {
        $this->model = $unit;
    }

    public function getActiveUnits()
    {
        return $this->model->select(['id', 'title'])
            ->whereNotNull('activated_at')
            ->orderBy('title', 'asc')->get();
    }
}
