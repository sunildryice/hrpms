<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\BloodGroup;

class BloodGroupRepository extends Repository
{
    public function __construct(BloodGroup $bloodGroup)
    {
        $this->model = $bloodGroup;
    }
}
