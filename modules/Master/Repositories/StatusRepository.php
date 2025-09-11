<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Status;

class StatusRepository extends Repository
{
    public function __construct(Status $status)
    {
        $this->model = $status;
    }
}
