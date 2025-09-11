<?php

namespace Modules\EmployeeAttendance\Repositories;

use App\Helper;
use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\EmployeeAttendance\Models\AttendanceDetailDonor;

class AttendanceDetailDonorRepository extends Repository
{
    public function __construct(AttendanceDetailDonor $attendanceDetailDonor, protected Helper $helper)
    {
        $this->model = $attendanceDetailDonor;
    }

    public function getDonorDetail($attendanceDetailId, $donorId): ?AttendanceDetailDonor
    {
        return $this->model->where('attendance_detail_id', '=', $attendanceDetailId)
            ->where('donor_id', '=', $donorId)
            // ->where('attendance_date', '=', $attendanceDate)
            ->first();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceDetailDonor = $this->model->create($inputs);
            DB::commit();

            return $attendanceDetailDonor;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceDetailDonor = $this->model->findOrFail($id);
            $attendanceDetailDonor->fill($inputs)->save();
            DB::commit();

            return $attendanceDetailDonor;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $attendanceDetailDonor = $this->model->findOrFail($id);
            $attendanceDetailDonor->delete();
            DB::commit();

            return $attendanceDetailDonor;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getTotalWorkedHours($attendanceDetailId)
    {
        $workedMinutes = 0.0;
        $donors =  $this->model->select(['worked_hours', 'attendance_detail_id'])->where('attendance_detail_id', '=', $attendanceDetailId)->get();
        foreach($donors as $donor){
            $workedMinutes += $this->helper->convertToMinutes($donor->worked_hours);
        }
        return $this->helper->convertToHourMinute($workedMinutes);
    }
}
