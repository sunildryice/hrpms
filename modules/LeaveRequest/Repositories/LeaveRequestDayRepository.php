<?php

namespace Modules\LeaveRequest\Repositories;

use App\Repositories\Repository;
use Modules\LeaveRequest\Models\LeaveRequestDay;

use DB;

class LeaveRequestDayRepository extends Repository
{
    public function __construct(LeaveRequestDay $leaveRequestDay)
    {
        $this->model = $leaveRequestDay;
    }

    /**
     * Get all employees on leave for a particular day
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getEmployeesOnLeave($date = null)
    {
        return $this->model->with(['leaveRequest.requester'])
            ->where('leave_date', $date ?: date('Y-m-d'))
            ->where('leave_duration', '>', 0)
            ->whereHas('leaveRequest', function ($q){
                $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
            })->get();
    }
}
