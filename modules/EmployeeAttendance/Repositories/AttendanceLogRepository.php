<?php

namespace Modules\EmployeeAttendance\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeAttendance\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AttendanceLogRepository extends Repository
{
    public function __construct(AttendanceLog $attendanceLog)
    {
        $this->model = $attendanceLog;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceLog = $this->model->create($inputs);
            DB::commit();
            return $attendanceLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceLog = $this->model->findOrFail($id);
            $attendanceLog->fill($inputs)->save();
            DB::commit();
            return $attendanceLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $attendanceLog = $this->model->findOrFail($id);
            $attendanceLog->delete();
            DB::commit();
            return $attendanceLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}