<?php

namespace Modules\LeaveRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\LeaveRequest\Models\LeaveRequestDay;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\LeaveMode;

class LeaveRequestRepository extends Repository
{
    public function __construct(
        protected Employee $employees,
        protected FiscalYear $fiscalYears,
        protected LeaveMode $leaveMode,
        LeaveRequest $leaveRequest,
        protected LeaveRequestDay $leaveRequestDays
    ) {
        $this->model = $leaveRequest;
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

    public function getLeaveRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'leave_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('leave_number') + 1;

        return $max;
    }

    public function getLeaveRequestsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get leave requests for a particular day
     *
     * @return mixed
     */
    public function getEmployeesOnLeave()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->whereHas('leaveDays', function ($query) {
                $query->where('leave_duration', '>', 0);
            })->with(['requester'])
            ->get();
    }

    public function getUpcomingLeaves()
    {
        $now = date('Y-m-d');
        $futureDate = now()->addDays(7)->format('Y-m-d');

        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('start_date', '>', $now)
            ->whereBetween('start_date', [$now, $futureDate])
            ->whereHas('leaveDays', function ($query) {
                $query->where('leave_duration', '>', 0);
            })->with(['requester'])
            ->get();

    }

    /**
     * Amend a leave request after approval to modify leave request
     *
     * @return false
     */
    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveRequest = $this->model->find($id);
            $leaveRequest->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $leaveRequest->replicate();
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = 1;
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_leave_request_id = $leaveRequest->id;
            $clone->modification_number = $this->model->where('modification_leave_request_id', $leaveRequest->id)
                ->max('modification_number') + 1;
            $clone->save();
            $substitutes = $leaveRequest->substitutes->pluck('id')->toArray();
            if ($substitutes) {
                $clone->substitutes()->sync($substitutes);
            }

            foreach ($leaveRequest->leaveDays as $leaveDay) {
                unset($leaveDay->id);
                unset($leaveDay->leave_request_id);
                $leaveDayInputs = $leaveDay->toArray();
                $leaveDayInputs['created_by'] = $inputs['created_by'];
                $clone->leaveDays()->create($leaveDayInputs);
            }
            $logInputs = [
                'user_id' => $inputs['created_by'],
                'status_id' => $leaveRequest->status_id,
                'log_remarks' => 'Leave request is amended.',
                'original_user_id' => $inputs['original_user_id'],
            ];
            $leaveRequest->logs()->create($logInputs);

            app(LeaveRepository::class)
                ->reconcileEmployeeLeave($leaveRequest->requester->employee, $leaveRequest->start_date->format('Y'), $leaveRequest->start_date->format('m'));

            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $leaveRequest->approver_id;
            }
            $leaveRequest->update($inputs);
            $leaveRequest->logs()->create($inputs);
            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                app(LeaveRepository::class)
                    ->reconcileEmployeeLeave($leaveRequest->requester->employee, $leaveRequest->start_date->format('Y'), $leaveRequest->start_date->format('m'));
            }

            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            if ($inputs['status_id'] == config('constant.RETURNED_STATUS')) {
                $inputs['log_remarks'] = $inputs['review_remarks'];
                unset($inputs['review_remarks']);
            } else {
                $inputs['log_remarks'] = 'Leave request is reviewed.';
            }
            $inputs['verifier_id'] = $inputs['user_id'];
            $leaveRequest = $this->model->find($id);
            $leaveRequest->update($inputs);
            $leaveRequest->logs()->create($inputs);
            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);

            return false;
        }
    }

    public function checkOverlapLeave($startDate, $endDate, $employeeId)
    {
        $leaveDays = false;
        if ($startDate && $endDate) {
            $dates = [];
            $current = strtotime($startDate);
            $last = strtotime($endDate);
            while ($current <= $last) {
                $dates[] = date('Y-m-d', $current);
                $current = strtotime('+1 day', $current);
            }
            $employee = $this->employees->find($employeeId);
            $leaveDays = $this->leaveRequestDays->select(['*'])
                ->whereHas('leaveRequest', function ($q) use ($employee) {
                    $q->where('requester_id', $employee->user->id);
                    $q->whereIn('status_id', [3, 4, 5, 6]);
                })->whereIn('leave_date', $dates)
                ->where('leave_duration', '<>', 0)
                ->count();
        }

        return $leaveDays;
    }

    public function checkOverlapLeaveDays(array $leaveDays, $employeeId)
    {
        $leaveDays = array_filter($leaveDays, function ($value) {
            return $value != config('constant.NO_LEAVE');
        });
        $employee = $this->employees->find($employeeId);
        $leaveDays = $this->leaveRequestDays->select(['*'])
            ->whereHas('leaveRequest', function ($q) use ($employee) {
                $q->where('requester_id', $employee->user->id);
                $q->whereIn('status_id', [3, 4, 5, 6]);
            })->whereIn('leave_date', array_keys($leaveDays))
            ->where('leave_duration', '<>', 0)
            ->count();

        return $leaveDays;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $leaveRequest = $this->model->create($inputs);
            if (array_key_exists('substitutes', $inputs)) {
                $leaveRequest->substitutes()->sync($inputs['substitutes']);
            }

            foreach ($inputs['leave_days'] as $key => $leaveDay) {
                $leaveMode = $this->leaveMode->find($inputs['leave_mode_id'][$key]);
                $hours = $leaveMode->hours;
                if ($leaveRequest->office->weekend_type == 1 && $leaveRequest->leaveType->leave_basis == 2) {
                    $weekday = date('w', strtotime($leaveDay));
                    $hours = $leaveMode->hours == 8 ? 7 : $leaveMode->hours;
                    $hours = $hours == 7 && $weekday == 5 ? 5 : $hours;
                }
                $leaveRequest->leaveDays()->create([
                    'leave_date' => $leaveDay,
                    'leave_mode_id' => $inputs['leave_mode_id'][$key],
                    'leave_duration' => $hours,
                    'leave_remarks' => $hours == 2 ? $inputs['leave_time'][$key] : null,
                    'created_by' => $inputs['updated_by'],
                ]);
            }

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['created_by'],
                    'log_remarks' => 'Leave request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $leaveRequest = $this->forward($leaveRequest->id, $forwardInputs);
            }

            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $leaveRequest = $this->model->findOrFail($id);
            if ($leaveRequest->parentLeaveRequest) {
                $parentLeaveRequest = $leaveRequest->parentLeaveRequest;
                $parentLeaveRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
                $employeeLeave = $leaveRequest->requester->employee->leaves()->where('leave_type_id', $parentLeaveRequest->leave_type_id)->first();
                $taken = $parentLeaveRequest->getLeaveDuration();
                $inputs = ['taken' => $employeeLeave->taken + $taken, 'balance' => $employeeLeave->balance - $taken];
                $employeeLeave->update($inputs);
            }
            $leaveRequest->logs()->delete();
            $leaveRequest->leaveDays()->delete();
            $leaveRequest->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveRequest = $this->model->findOrFail($id);
            if ($leaveRequest->getLeaveDifferenceInDays() > 3) {
                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            } else {
                $inputs['status_id'] = config('constant.VERIFIED_STATUS');
            }
            if (! $leaveRequest->leave_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'LR';
                $inputs['request_date'] = date('Y-m-d');
                $inputs['leave_number'] = $this->getLeaveRequestNumber($fiscalYear);
            }
            $leaveRequest->update($inputs);
            $leaveRequest->logs()->create($inputs);
            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $leaveRequest = $this->model->find($id);
            $leaveRequest->fill($inputs)->save();
            $leaveRequest->leaveDays()->delete();
            if (! $leaveRequest->modification_number) {
                if (array_key_exists('substitutes', $inputs)) {
                    $leaveRequest->substitutes()->sync($inputs['substitutes']);
                } else {
                    $leaveRequest->substitutes()->sync([]);
                }
            }

            foreach ($inputs['leave_days'] as $key => $leaveDay) {
                $leaveMode = $this->leaveMode->find($inputs['leave_mode_id'][$key]);
                $hours = $leaveMode->hours;
                if ($leaveRequest->office->weekend_type == 1 && $leaveRequest->leaveType->leave_basis == 2) {
                    $weekday = date('w', strtotime($leaveDay));
                    $hours = $leaveMode->hours == 8 ? 7 : $leaveMode->hours;
                    $hours = $hours == 7 && $weekday == 5 ? 5 : $hours;
                }
                $leaveRequest->leaveDays()->create([
                    'leave_date' => $leaveDay,
                    'leave_mode_id' => $inputs['leave_mode_id'][$key],
                    'leave_duration' => $hours,
                    'leave_remarks' => $hours == 2 ? $inputs['leave_time'][$key] : null,
                    'created_by' => $inputs['updated_by'],
                ]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Leave request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $leaveRequest = $this->forward($leaveRequest->id, $forwardInputs);
            }

            DB::commit();

            return $leaveRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
