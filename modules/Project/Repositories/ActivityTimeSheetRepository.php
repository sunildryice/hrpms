<?php

// repo for project activity timesheet
namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Project\Models\ActivityTimeSheet;

class ActivityTimeSheetRepository extends Repository
{
    public function __construct(ActivityTimeSheet $model)
    {
        $this->model = $model;
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

    public function getMonthlyTimeSheets()
    {
        return $this->model
            ->from($this->model->getTable() . ' as tst')
            ->join('projects as p', 'tst.project_id', '=', 'p.id')
            ->selectRaw("
            DATE_FORMAT(tst.timesheet_date, '%Y-%m')           AS month,
            ANY_VALUE(DATE_FORMAT(tst.timesheet_date, '%M %Y')) AS month_name,
            
            COUNT(*)                                           AS total_entries,
            ROUND(SUM(tst.hours_spent), 2)                     AS total_hours,
            
            COUNT(DISTINCT tst.project_id)                     AS unique_project_count,
            GROUP_CONCAT(DISTINCT p.short_name 
                         ORDER BY p.short_name 
                         SEPARATOR ', ')                       AS project_short_names
        ")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }
}
