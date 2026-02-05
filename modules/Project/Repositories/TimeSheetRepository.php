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

    /**
     * Get summary counts per year + month
     */
    public function getMonthlySummary()
    {
        return $this->model
            ->selectRaw('
                `year`,
                `month`,
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
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderByRaw('MONTH(start_date) ASC')
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
                'requester',
                'status',
                'approvedLog' => function ($q) {
                    $q->latest();
                }
            ])
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('status_id')
            ->orderBy('requester_id')
            ->get();
    }

    /**
     * Alternative: grouped by status (if you prefer separate columns/sections)
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