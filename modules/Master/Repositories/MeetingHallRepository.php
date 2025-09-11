<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\MeetingHall;

class MeetingHallRepository extends Repository
{
    public function __construct(MeetingHall $meetingHall)
    {
        $this->model = $meetingHall;
    }
}
