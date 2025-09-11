<?php

namespace Modules\ExitStaffClearance\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\ExitStaffClearance\Models\StaffClearanceRecord;

class StaffClearanceRecordRepository extends Repository
{
    public function __construct(StaffClearanceRecord $staffClearanceRecords)
    {
        $this->model = $staffClearanceRecords;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['created_by'] = auth()->user()->id;
            $staffClearanceRecords = $this->model->create($inputs);
            DB::commit();
            return $staffClearanceRecords;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['updated_by'] = auth()->user()->id;
            $staffClearanceRecords = $this->model->findOrFail($id);
            $staffClearanceRecords->fill($inputs)->save();
            DB::commit();
            return $staffClearanceRecords;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}
