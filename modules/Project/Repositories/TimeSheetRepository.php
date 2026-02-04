<?php

// repo for project activity timesheet
namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Project\Models\TimeSheet;
use Illuminate\Database\QueryException;

class TimeSheetRepository extends Repository
{
    public function __construct(TimeSheet $model)
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

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $timeSheet = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $timeSheet->update($inputs);
            $timeSheet->logs()->create($inputs);
            DB::commit();
            return $timeSheet;
        } catch (QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $timeSheet = $this->model->find($id);
            $timeSheet->fill($inputs)->save();

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'TimeSheet is submitted.',
                ];
                $timeSheet = $this->forward($timeSheet->id, $forwardInputs);
            }
            DB::commit();
            return $timeSheet;
        } catch (QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
