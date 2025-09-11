<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\Master\Models\DonorCode;

class DonorCodeRepository extends Repository
{
    public function __construct(DonorCode $donorCode, protected AttendanceRepository $attendances)
    {
        $this->model = $donorCode;
    }

    public function getActiveDonorCodes()
    {
        return $this->model->select(['id', 'title', 'description', 'activated_at', 'attendance_enable_at'])
            ->whereNotNull('activated_at')
            ->orderBy('description', 'asc')->get();
    }

    public function getEnabledDonorCodes()
    {
        return $this->model->select(['id', 'title', 'description', 'activated_at', 'attendance_enable_at'])
            ->whereNotNull('activated_at')
            ->whereNotNull('attendance_enable_at')
            ->orderBy('description', 'asc')->get();
    }

    public function getArrayOfEnabledDonorCodes()
    {
        $donorCodes = $this->model->whereNotNull('activated_at')
            ->whereNotNull('attendance_enable_at')
            ->pluck('id')
            ->toArray();

        return $donorCodes;
    }

    public function getUnrestrictedDonor()
    {
        return $this->model->where('title', config('constant.UNRESTRICTED_DONOR'))->first();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $donorCode = $this->model->create($inputs);
            if (isset($donorCode->attendance_enable_at)) {
                $this->updateAttendanceDonors($donorCode->id, true);
            }
            DB::commit();

            return $donorCode;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $donorCode = $this->model->findOrFail($id);
            $donorCode->fill($inputs)->save();
            if (isset($donorCode->attendance_enable_at)) {
                $this->updateAttendanceDonors($donorCode->id, true);
            }
            DB::commit();

            return $donorCode;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    /**
     *  Update Donor codes in attendance with created_status
     *
     * @param  mixed  $id  Donor code id
     * @param  mixed  $isAdd  True if adding donor code, False if removing donor code
     * @return void
     */
    public function updateAttendanceDonors($id, $isAdd)
    {
        $attendances = $this->attendances->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])->get();
        foreach ($attendances as $attendance) {
            $donorCodes = explode(',', $attendance->donor_codes);
            if ($isAdd && ! in_array($id, $donorCodes)) {
                $donorCodes[] = $id;
            } elseif (! $isAdd && ($key = array_search($id, $donorCodes)) !== false) {
                unset($donorCodes[$key]);
            } else {
                continue;
            }
            $attendance->donor_codes = implode(',', $donorCodes);
            $attendance->save();
        }
    }
}
