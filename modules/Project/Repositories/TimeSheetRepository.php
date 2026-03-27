<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Project\Models\TimeSheet;

class TimeSheetRepository extends Repository
{
    public function __construct(TimeSheet $model)
    {
        $this->model = $model;
    }

    public function getTimeSheetOfUserByYearAndMonth($requesterId, $year, $month)
    {
        return $this->model->select(['*'])
            ->where('requester_id', $requesterId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    public function getLatestApprovedTimesheet($authUserId)
    {
        return $this->model->select(['*'])
            ->where('requester_id', $authUserId)
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.SUBMITTED_STATUS'),])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();
    }

    /**
     * Get summary counts per year + month
     */
    public function getMonthlySummary()
    {
        return $this->model
            ->selectRaw('
                `year`,
                `month`,
                `month_name`,
                COUNT(CASE WHEN status_id = ? THEN 1 END) AS not_submitted,
                COUNT(CASE WHEN status_id = ? THEN 1 END) AS submitted,
                COUNT(CASE WHEN status_id = ? THEN 1 END) AS approved,
                COUNT(CASE WHEN status_id = ? THEN 1 END) AS returned
            ', [
                config('constant.CREATED_STATUS', 1),
                config('constant.SUBMITTED_STATUS', 3),
                config('constant.APPROVED_STATUS', 6),
                config('constant.RETURNED_STATUS', 2),
            ])
            ->groupBy('year', 'month', 'month_name')
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get all timesheets for specific year + month
     * with necessary relations
     */
    public function getTimesheetsByYearAndMonth(string $year, string $month)
    {
        return $this->model
            ->with([
                'requester.employee',
                'approver.employee',
                'status',
                'approvedLog' => function ($q) {
                    $q->latest();
                }
            ])
            ->whereHas('requester.employee', function ($q) {
                $q->whereNotNull('activated_at');
                // ->where(function ($sub) {
                //     $sub->where('employee_type_id', config('constant.FULL_TIME_EMPLOYEE'))
                //         ->orWhereNull('employee_type_id');
                // });
            })
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('status_id')
            ->orderBy('requester_id')
            ->get();
    }

    /**
     * Grouped by status (separate columns/sections)
     */
    public function getTimesheetsGroupedByStatus(string $year, string $month)
    {
        return $this->model
            ->select('status_id', 'requester_id')
            ->with('requester:id,first_name,last_name')
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->groupBy('status_id');
    }
}
