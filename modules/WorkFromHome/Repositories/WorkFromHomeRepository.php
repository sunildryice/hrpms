<?php

namespace Modules\WorkFromHome\Repositories;

use App\Repositories\Repository;
use Modules\WorkFromHome\Models\WorkFromHome;

class WorkFromHomeRepository extends Repository
{

    public function __construct(protected WorkFromHome $workFromHome)
    {
        $this->model = $workFromHome;
    }


    public function getWorkFromHomeRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'work_from_home_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('work_from_home_number') + 1;
        return $max;
    }

    public function getWorkFromHomeRequestsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getEmployeesOnWorkFromHome()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))->with(['requester'])
            ->get();
    }

    public function getUpcomingWorkFromHomes()
    {
        $now = date('Y-m-d');
        $futureDate = now()->addDays(7)->format('Y-m-d');

        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '>', $now)
            ->whereBetween('start_date', [$now, $futureDate])->with(['requester'])
            ->get();
    }
}
