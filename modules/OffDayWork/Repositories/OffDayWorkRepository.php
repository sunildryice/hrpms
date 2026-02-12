<?php

namespace Modules\OffDayWork\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProjectCode;
use Modules\OffDayWork\Models\OffDayWork;

class OffDayWorkRepository extends Repository
{
    public function __construct(protected OffDayWork $offDayWork)
    {
        $this->model = $offDayWork;
    }

    public function getOffDayWorkRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'off_day_work_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('off_day_work_number') + 1;

        return $max;
    }

    public function getAvailableOffDayWorkDates($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status_id', config('constants.APPROVED_STATUS'))
            ->pluck('off_day_work_date')
            ->toArray();
    }
}
