<?php

namespace Modules\EmployeeRequest\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeRequest\Models\EmployeeRequest;

use DB;

class EmployeeRequestRepository extends Repository
{
    public function __construct(
        EmployeeRequest $employeeRequest
    )
    {
        $this->model = $employeeRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                // Here, we are doing orderByDesc in the relation's column
                return $this->model
                    ->with(['fiscalYear', 'status', 'dutyStation', 'logs'])->select(['*'])
                    ->whereStatusId(config('constant.APPROVED_STATUS'))
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->get()
                    ->sortByDesc(function ($approvedItem, $key) {
                        return $approvedItem->logs->last(function ($log, $key) {
                            return $log->status_id == config('constant.APPROVED_STATUS');
                        })?->created_at;
                    });
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status', 'dutyStation', 'logs'])->select(['*'])
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->whereIn('office_id', $accessibleOfficeIds)
            ->get()
            ->sortByDesc(function ($approvedItem, $key) {
                return $approvedItem->logs->last(function ($log, $key) {
                    return $log->status_id == config('constant.APPROVED_STATUS');
                })?->created_at;
            });
    }

    public function getEmployeeRequestNumber()
    {
        $max = $this->model->max('employee_request_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $employeeRequest->approver_id;
            }
            $employeeRequest->update($inputs);
            $employeeRequest->logs()->create($inputs);
            DB::commit();
            return $employeeRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $employeeRequest = $this->model->create($inputs);
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['created_by'],
                    'log_remarks' => 'Employee request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $employeeRequest = $this->forward($employeeRequest->id, $forwardInputs);
            }
            DB::commit();
            return $employeeRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $employeeRequest = $this->model->findOrFail($id);
            $employeeRequest->logs()->delete();
            $employeeRequest->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $employeeRequest->update($inputs);
            $employeeRequest->logs()->create($inputs);
            DB::commit();
            return $employeeRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeRequest = $this->model->find($id);
            $employeeRequest->update($inputs);
            $employeeRequest->logs()->create($inputs);
            DB::commit();
            return $employeeRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeRequest = $this->model->find($id);
            $employeeRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Employee request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $employeeRequest = $this->forward($employeeRequest->id, $forwardInputs);
            }
            DB::commit();
            return $employeeRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
