<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Tenure;

use DB;

class TenureRepository extends Repository
{
    public function __construct(Tenure $tenure)
    {
        $this->model = $tenure;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $tenure = $this->model->create($inputs);
            $tenure->employee->update([
                'department_id' => $tenure->department_id,
                'designation_id' => $tenure->designation_id,
                'office_id' => $tenure->office_id,
                'supervisor_id' => $tenure->supervisor_id,
                'cross_supervisor_id' => $tenure->cross_supervisor_id,
                'next_line_manager_id' => $tenure->next_line_manager_id,
            ]);
            DB::commit();
            return $tenure;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $tenure = $this->model->findOrFail($id);
            $tenure->fill($inputs)->save();
            if($tenure->id == $tenure->employee->latestTenure->id){
                $tenure->employee->update([
                    'department_id' => $tenure->department_id,
                    'designation_id' => $tenure->designation_id,
                    'office_id' => $tenure->office_id,
                    'supervisor_id' => $tenure->supervisor_id,
                    'cross_supervisor_id' => $tenure->cross_supervisor_id,
                    'next_line_manager_id' => $tenure->next_line_manager_id,
                ]);
            }
            DB::commit();
            return $tenure;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
