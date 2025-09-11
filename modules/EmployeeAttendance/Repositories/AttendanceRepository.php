<?php

namespace Modules\EmployeeAttendance\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\Master\Repositories\DonorCodeRepository;

class AttendanceRepository extends Repository
{
    private $donors;

    public function __construct(
        Attendance $attendance,
    ) {
        $this->model = $attendance;
    }

    public function getAttendanceObject($employeeId, $year, $month)
    {
        return $this->model->where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function getPending()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $inputs['donor_codes'] = implode(',', app(DonorCodeRepository::class)->getArrayOfEnabledDonorCodes());
            $attendance = $this->model->create($inputs);
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($id);
            $attendance->fill($inputs)->save();
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function submit($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->find($id);
            $attendance->fill($inputs)->save();

            if ($inputs['btn'] == 'submit') {
                $inputs['status_id'] = 3;
                $attendance->update($inputs);
                $forwardInputs = [
                    'user_id' => $inputs['user_id'],
                    'log_remarks' => 'Attendance submitted. '.$inputs['remarks'],
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                ];
                $attendance->logs()->create($forwardInputs);
            }
            $attendance = $this->model->find($id);
            DB::commit();

            return $attendance;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($id);
            $attendance->delete();
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($id);
            $attendance->status_id = config('constant.RETURNED_STATUS');
            $attendance->save();
            $attendance->logs()->create($inputs);
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function verify($attendanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($attendanceId);
            $attendance->update($inputs);
            $attendance->logs()->create($inputs);
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function approve($attendanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($attendanceId);
            $attendance->update($inputs);
            $attendance->logs()->create($inputs);
            DB::commit();

            return $attendance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }
}
