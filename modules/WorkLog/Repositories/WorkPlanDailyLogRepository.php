<?php

namespace Modules\WorkLog\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\WorkLog\Models\WorkPlanDailyLog;

class WorkPlanDailyLogRepository extends Repository
{
    public function __construct(WorkPlanDailyLog $workPlanDailyLog)
    {
        $this->model = $workPlanDailyLog;
    }

    public function getDailyLog($workPlanId, $logDate, $donorId = null): ?WorkPlanDailyLog
    {
        return $this->model->select('*')
            ->where('work_plan_id', $workPlanId)
            ->where('log_date', $logDate)
            ->when($donorId, function ($query) use ($donorId) {
                return $query->where('donor_id', $donorId);
            })->first();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $workPlanDailyLog = $this->model->create($inputs);
            DB::commit();

            return $workPlanDailyLog;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $workPlanDailyLog = $this->model->find($id);
            $workPlanDailyLog->fill($inputs)->save();
            DB::commit();

            return $workPlanDailyLog;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
