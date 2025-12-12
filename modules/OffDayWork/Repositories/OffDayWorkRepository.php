<?php

namespace Modules\OffDayWork\Repositories;


use App\Repositories\Repository;
use Modules\OffDayWork\Models\OffDayWork;

class OffDayWorkRepository extends Repository
{
    public function __construct(protected OffDayWork $offDayWork)
    {
        $this->model = $offDayWork;
    }
}
