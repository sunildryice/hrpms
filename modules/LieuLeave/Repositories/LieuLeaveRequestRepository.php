<?php

namespace Modules\LieuLeave\Repositories;

use App\Repositories\Repository;
use Modules\LieuLeave\Models\LieuLeaveRequest;

class LieuLeaveRequestRepository extends Repository
{
    public function __construct(protected LieuLeaveRequest $lieuLeaveRequest)
    {
        $this->model = $lieuLeaveRequest;
    }

    public function getLieuLeaveRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'lieu_leave_request_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('lieu_leave_request_number') + 1;
        return $max;
    }
}
