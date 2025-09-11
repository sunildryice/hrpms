<?php

namespace Modules\LeaveRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Models\LeaveEncash;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LeaveMode;

class LeaveEncashRepository extends Repository
{

    private $employees;
    private $fiscalYears;
    private $leaveMode;
    private $leaveEncash;

    public function __construct(
        Employee $employees,
        FiscalYear $fiscalYears,
        LeaveMode $leaveMode,
        LeaveEncash $leaveEncash,
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->leaveMode = $leaveMode;
        $this->model = $leaveEncash;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) use ($authUser) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);
                    })->orWhere('employee_id', $authUser->employee_id)
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
            ->orderBy('created_at', 'desc')->get();
    }

    public function getLeaveEncashNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'encash_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('encash_number') + 1;
        return $max;
    }

    public function getLeaveEncashsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getPaid()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status'])
                    ->select(['*'])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->whereStatusId(config('constant.PAID_STATUS'))
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.PAID_STATUS')]);
                    })
                    ->orderBy('request_date', 'desc')->get();
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->orderBy('sheet_number', 'desc')->get();
    }

    /**
     * Amend a leave encash after approval to modify leave request
     *
     * @param $id
     * @param $inputs
     * @return false
     */
    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->find($id);
            $leaveEncash->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $leaveEncash->replicate();
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = 1;
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_leave_request_id = $leaveEncash->id;
            $clone->modification_number = $this->model->where('modification_leave_request_id', $leaveEncash->id)
                ->max('modification_number') + 1;
            $clone->save();

            $logInputs = [
                'user_id' => $inputs['created_by'],
                'status_id' => $leaveEncash->status_id,
                'log_remarks' => 'Leave request is amended.',
                'original_user_id' => $inputs['original_user_id'],
            ];
            $leaveEncash->logs()->create($logInputs);

            app(LeaveRepository::class)
                ->reconcileEmployeeLeave($leaveEncash->employee, $leaveEncash->request_date->format('Y'), $leaveEncash->request_date->format('m'));

            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $leaveEncash->approver_id;
            }
            $leaveEncash->update($inputs);
            $leaveEncash->logs()->create($inputs);
            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                app(LeaveRepository::class)
                    ->reconcileEmployeeLeave($leaveEncash->employee, $leaveEncash->request_date->format('Y'), $leaveEncash->request_date->format('m'));
            }

            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->find($id);
            $leaveEncash->update($inputs);
            $leaveEncash->logs()->create($inputs);
            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $leaveEncash->update($inputs);
            $leaveEncash->logs()->create($inputs);
            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $leaveEncash = $this->model->create($inputs);
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['created_by'],
                    'log_remarks' => 'Leave Encash Request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $leaveEncash = $this->forward($leaveEncash->id, $forwardInputs);
            }

            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $leaveEncash = $this->model->findOrFail($id);
            $leaveEncash->logs()->delete();
            $leaveEncash->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$leaveEncash->encash_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'LER';
                $inputs['request_date'] = date('Y-m-d');
                $inputs['encash_number'] = $this->getLeaveEncashNumber($fiscalYear);
            }
            $leaveEncash->update($inputs);
            $leaveEncash->logs()->create($inputs);
            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveEncash = $this->model->find($id);
            $leaveEncash->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Leave request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $leaveEncash = $this->forward($leaveEncash->id, $forwardInputs);
            }

            DB::commit();
            return $leaveEncash;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
