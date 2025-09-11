<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Designation;

class DesignationRepository extends Repository
{
    public function __construct(Designation $designation)
    {
        $this->model = $designation;
    }

    public function getActiveDesignations()
    {
        return $this->model->select(['*'])
            ->orderBy('title')->get();
    }
}
