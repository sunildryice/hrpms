<?php

namespace Modules\LieuLeave\Repositories;

use Modules\LieuLeave\Models\LieuLeaveLog;
use App\Repositories\BaseRepository;
use App\Repositories\Repository;
use Modules\LieuLeave\Models\LieuLeaveRequest;

class LieuLeaveRequestRepository extends Repository
{
    public function __construct(protected LieuLeaveRequest $lieuLeaveRequest)
    {
        $this->model = $lieuLeaveRequest;
    }
}
