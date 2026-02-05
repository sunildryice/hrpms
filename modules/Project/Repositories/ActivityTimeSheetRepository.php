<?php
namespace Modules\Project\Repositories;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Project\Models\TimeSheet;
use Illuminate\Database\QueryException;
use Modules\Project\Models\ActivityTimeSheet;

class ActivityTimeSheetRepository extends Repository
{
    public function __construct(ActivityTimeSheet $model)
    {
        $this->model = $model;
    }
    public function getQuery()
    {
        return $this->model;
    }
    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->create($inputs);
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }
    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->update($inputs);
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            dd($e);
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->delete();
            DB::commit();
            return true;
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }
    public function getMonthlyTimeSheets($userId = null)
    {
        return TimeSheet::with('status')
            ->select([
                'timesheets.*',
                DB::raw("CONCAT(timesheets.year, '-', LPAD(MONTH(timesheets.start_date), 2, '0')) AS month"),
                DB::raw("CONCAT(timesheets.month, ' ', timesheets.year) AS month_name"),
                DB::raw("(SELECT COUNT(*) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS total_entries"),
                DB::raw("(SELECT ROUND(SUM(tst.hours_spent), 2) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS total_hours"),
                DB::raw("(SELECT COUNT(DISTINCT tst.project_id) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS unique_project_count"),
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.short_name ORDER BY p.short_name SEPARATOR ', ') 
                      FROM project_activity_timesheet tst 
                      JOIN projects p ON tst.project_id = p.id 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS project_short_names"),
            ])
            ->when($userId, function ($query) use ($userId) {
                return $query->where('timesheets.requester_id', $userId);
            })
            ->orderBy('timesheets.year', 'asc')
            ->orderByRaw('MONTH(timesheets.start_date) asc')
            ->get();
    }

    public function getApproverMonthlyTimeSheets($approverId = null)
    {
        return TimeSheet::with('status', 'requester')
            ->select([
                'timesheets.*',
                DB::raw("CONCAT(timesheets.year, '-', LPAD(MONTH(timesheets.start_date), 2, '0')) AS month"),
                DB::raw("CONCAT(timesheets.month, ' ', timesheets.year) AS month_name"),
                DB::raw("(SELECT COUNT(*) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS total_entries"),
                DB::raw("(SELECT ROUND(SUM(tst.hours_spent), 2) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS total_hours"),
                DB::raw("(SELECT COUNT(DISTINCT tst.project_id) FROM project_activity_timesheet tst 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS unique_project_count"),
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.short_name ORDER BY p.short_name SEPARATOR ', ') 
                      FROM project_activity_timesheet tst 
                      JOIN projects p ON tst.project_id = p.id 
                      WHERE tst.created_by = timesheets.requester_id 
                      AND tst.timesheet_date BETWEEN timesheets.start_date AND timesheets.end_date) AS project_short_names"),
            ])
            ->when($approverId, function ($query) use ($approverId) {
                $query->where('approver_id', $approverId);
            })
            ->where('status_id', config('constant.SUBMITTED_STATUS')) 
            ->orderBy('timesheets.year', 'asc')
            ->orderByRaw('MONTH(timesheets.start_date) asc')
            ->get();
    }
    public function getTimeSheetsByPeriod($startDate, $endDate, $userId = null)
    {
        return $this->model
            ->when($userId, function ($query) use ($userId) {
                return $query->where('created_by', $userId);
            })
            ->whereBetween('timesheet_date', [$startDate, $endDate])
            ->with(['project', 'activity'])
            ->get();
    }
}