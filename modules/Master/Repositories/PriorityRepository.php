<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Priority;

class PriorityRepository extends Repository
{
    public function __construct(Priority $priority)
    {
        $this->model = $priority;
    }
}
