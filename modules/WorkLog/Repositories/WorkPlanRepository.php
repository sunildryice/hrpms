<?php

namespace Modules\WorkLog\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\WorkLog\Models\WorkPlan;

class WorkPlanRepository extends Repository
{
    public function __construct(WorkPlan $workPlan)
    {
        $this->model = $workPlan;
    }

    public function getWorkPlan($year, $month, $employeeId): ?WorkPlan
    {
        return $this->model->where('year', $year)->where('month', $month)
            ->where('employee_id', $employeeId)->first();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $workPlan = $this->model->find($id);
            $workPlan->update($inputs);
            $userId = auth()->id();
            $forwardInputs = [
                'work_plan_id' => $id,
                'user_id' => $userId,
                'log_remarks' => $inputs['log_remarks'],
                'original_user_id' => $inputs['original_user_id'],
                'status_id' => $inputs['status_id'],
            ];
            $workPlan->logs()->create($forwardInputs);

            DB::commit();

            return $workPlan;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }

    }

    public function updateOrCreate($inputs, $attributes = [])
    {
        DB::beginTransaction();
        try {
            $workPlan = $this->model->updateOrCreate(['employee_id' => $inputs['employee_id'], 'year' => $inputs['year'], 'month' => $inputs['month']], $inputs);
            DB::commit();

            return $workPlan;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $workPlan = $this->model->findOrFail($id);
            $workPlan->logs()->delete();
            $workPlan->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function submit($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $workPlan = $this->model->find($id);
            $userId = auth()->id();
            $workPlan->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $inputs['status_id'] = 3;
                $workPlan->update($inputs);
                $forwardInputs = [
                    'work_plan_id' => $id,
                    'user_id' => $userId ?: $workPlan->requester_id,
                    'log_remarks' => 'Work log is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $inputs['status_id'],
                ];
                $workPlan->logs()->create($forwardInputs);
            }
            $workPlan = $this->model->find($id);
            DB::commit();

            return $workPlan;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function saveAttWorklog($inputs)
    {
        DB::beginTransaction();
        try {
            $workPlan = $this->model->find($id);
            $userId = auth()->id();
            $workPlan->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $inputs['status_id'] = 3;
                $workPlan->update($inputs);
                $forwardInputs = [
                    'work_plan_id' => $id,
                    'user_id' => $userId ?: $workPlan->requester_id,
                    'log_remarks' => 'Work log is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $inputs['status_id'],
                ];
                $workPlan->logs()->create($forwardInputs);
            }
            $workPlan = $this->model->find($id);
            DB::commit();

            return $workPlan;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
