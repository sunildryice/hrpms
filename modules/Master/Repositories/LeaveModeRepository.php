<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\LeaveMode;

class LeaveModeRepository extends Repository
{
    public function __construct(LeaveMode $leaveMode)
    {
        $this->model = $leaveMode;
    }

    public function getRequestLeaveModes()
    {
        return $this->model->where('hours', '<>', 0)->get();
    }
}
