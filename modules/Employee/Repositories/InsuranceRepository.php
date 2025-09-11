<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Insurance;

class InsuranceRepository extends Repository
{
    public function __construct(Insurance $insurance)
    {
        $this->model = $insurance;
    }
}
