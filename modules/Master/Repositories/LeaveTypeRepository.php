<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\LeaveType;

class LeaveTypeRepository extends Repository
{
    public function __construct(LeaveType $leaveType)
    {
        $this->model = $leaveType;
    }

    public function getActivated()
    {
        return $this->model->whereNotNull('activated_at')->get();
    }
}
