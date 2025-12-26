<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use DB;
use Modules\Employee\Models\Leave;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestDayRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;

class LeaveRepository extends Repository
{
    public function __construct(
        Leave $leave,
        protected FiscalYearRepository $fiscalYears,
        protected LeaveRequestDayRepository $leaveRequestDays,
        protected LeaveRequestRepository $leaveRequests,
        protected LeaveTypeRepository $leaveTypes,
        protected LeaveEncashRepository $leaveEncash,
    ) {
        $this->model = $leave;
    }

    public function getEmployeeLeaves($employeeId, $fiscalYearId = null)
    {
        $fiscalYear = $fiscalYearId ? $this->fiscalYears->find($fiscalYearId) : $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $sql = 'SELECT u1.* FROM employee_leaves u1
            WHERE u1.employee_id=? AND u1.reported_date = (SELECT MAX(u2.reported_date)
                                                           FROM employee_leaves u2 WHERE u2.employee_id=? AND u2.leave_type_id = u1.leave_type_id and YEAR(u2.reported_date)=?)';
        $leaveIds = DB::select($sql, [$employeeId, $employeeId, $fiscalYear->title]);

        return  $this->model
            ->with(['leaveType' => function ($q) {
                $q->select(['*', 'title']);
            }])->whereIn('id', array_column($leaveIds, 'id'))
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('leaveType', function ($q) {
                $q->whereNotNull('activated_at');
            })
            ->get();
    }

    public function getEmployeeLeavesForCurrentFiscalYear($employeeId)
    {
        return $this->model->with(['leaveType'])
            ->where('employee_id', '=', $employeeId)
            ->where('fiscal_year_id', '=', $this->fiscalYears->getCurrentFiscalYearId())
            ->whereHas('leaveType', function ($q) {
                $q->whereNotNull('activated_at');
            })->get();
    }

    public function getMonthlyEmployeeLeaves($employeeId, $year, $month)
    {
        $monthlyLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereMonth('reported_date', date($month))
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 2);
            })->get();

        $yearlyLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereMonth('reported_date', date($month))
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 1);
            })->get();

        $specialLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereMonth('reported_date', date($month))
            ->whereHas('leaveType', function ($q) {
                $q->whereNotIn('leave_frequency', [1, 2]);
            })->get();

        $leaves = $monthlyLeaves->merge($yearlyLeaves)->merge($specialLeaves);

        return $leaves;
    }

    public function getYearlyEmployeeLeaves($employeeId, $year)
    {
        $monthlyLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 2);
            })->get();

        $yearlyLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_frequency', 1);
            })->get();

        $specialLeaves = $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('reported_date', $year)
            ->whereHas('leaveType', function ($q) {
                $q->whereNotIn('leave_frequency', [1, 2]);
            })->get();

        $leaves = $monthlyLeaves->merge($yearlyLeaves)->merge($specialLeaves);

        return $leaves;
    }

    public function reconcileEmployeeLeave($employee, $year, $month)
    {
        DB::beginTransaction();
        try {
            $monthLeaves = $this->model->with(['leaveType'])
                ->where('employee_id', $employee->id)
                ->whereYear('reported_date', $year)
                ->whereMonth('reported_date', $month)
                ->get();

            foreach ($monthLeaves as $employeeLeave) {
                $leaveRequestIds = $this->leaveRequests->select(['id'])
                    ->where('requester_id', $employee->user->id)
                    ->where('leave_type_id', $employeeLeave->leave_type_id)
                    ->whereStatusId(config('constant.APPROVED_STATUS'))
                    ->pluck('id')->toArray();

                $paid = $this->leaveEncash->select(['*'])
                    ->where('leave_type_id', $employeeLeave->leave_type_id)
                    ->where('employee_id', $employee->id)
                    ->whereYear('request_date', $year)
                    ->whereMonth('request_date', $month)
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
                    ->sum('encash_balance');
                $taken = 0;
                if ($leaveRequestIds || $employeeLeave->paid) {
                    if ($employeeLeave->leaveType->leave_frequency == 2) {
                        $taken = $this->leaveRequestDays->select(['*'])
                            ->whereIn('leave_request_id', $leaveRequestIds)
                            ->whereYear('leave_date', $year)
                            ->whereMonth('leave_date', $month)
                            ->sum('leave_duration');
                    } else {
                        $taken = $this->leaveRequestDays->select(['*'])
                            ->whereIn('leave_request_id', $leaveRequestIds)
                            ->whereYear('leave_date', $year)
                            ->sum('leave_duration');
                    }

                    if ($taken && $employeeLeave->leaveType->leave_basis != 2) {
                        $taken = round($taken / 8, 2);
                    }

                    $balance = $employeeLeave->opening_balance + $employeeLeave->earned - $taken - $employeeLeave->paid;
                    $employeeLeave->update([
                        'balance' => $balance,
                        'taken' => $taken,
                    ]);
                }

                if ($paid) {
                    $balance = $employeeLeave->opening_balance + $employeeLeave->earned - $employeeLeave->taken - $paid;
                    $employeeLeave->update([
                        'balance' => $balance,
                        'paid' => $paid,
                    ]);
                }

                $employeeLeave = $this->model->find($employeeLeave->id);
                if ($employeeLeave->paid || $leaveRequestIds) {
                    $nextMonthYear = ($month == 12) ? $year + 1 : $year;
                    $nextMonth = ($month == 12) ? 01 : $month + 1;

                    $nextMonthLeave = $this->model->select(['*'])
                        ->where('employee_id', $employee->id)
                        ->where('leave_type_id', $employeeLeave->leave_type_id)
                        ->whereYear('reported_date', $nextMonthYear)
                        ->whereMonth('reported_date', $nextMonth)
                        ->first();

                    if ($nextMonthLeave) {
                        $nextMonthLeave->update([
                            'opening_balance' => $employeeLeave->balance,
                        ]);
                    }
                }
                unset($employeeLeave);
            }
            DB::commit();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function generateEmployeeLeave($employee, $reportedDate, $fiscalYear, $previousYear)
    {
        DB::beginTransaction();
        try {
            $month = date('m', strtotime($reportedDate));
            $leaveTypes = $this->leaveTypes->select('*')
                ->whereNotNull('activated_at')->get();

            foreach ($leaveTypes as $leaveType) {
                $insertFlag = true;
                if ($leaveType->female) {
                    $insertFlag = $employee->gender == 2;
                } elseif ($leaveType->male) {
                    $insertFlag = $employee->gender == 1;
                }
                $employeeLeave = $this->model->select(['*'])
                    ->where('employee_id', $employee->id)
                    ->where('fiscal_year_id', $fiscalYear->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->first();

                if (! $employeeLeave && $insertFlag) {
                    if ($employee->joined_date && $employee->activated_at) {
                        $this->firstLeaveEarning($leaveType, $employee, $reportedDate, $fiscalYear, $previousYear);
                    }
                }
                $this->recurringMonthlyLeaveEarning($leaveType, $employee, $reportedDate, $fiscalYear, $previousYear);
            }
            DB::commit();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    protected function firstLeaveEarning($leaveType, $employee, $reportedDate, $fiscalYear, $previousYear)
    {
        $reportedDate = Carbon::createFromDate($reportedDate);
        $reportedDateMonthDays = $reportedDate->daysInMonth;
        $percentile = $this->getWorkPercentile($employee, $reportedDate, $fiscalYear);
        $generateFlag = false;
        if ($reportedDate->format('m') == $employee->joined_date->format('m') && $reportedDate->format('Y') == $employee->joined_date->format('Y')) {
            $generateFlag = true;
        } elseif ($reportedDate->format('m') == 1 && $employee->joined_date < $reportedDate) {
            $generateFlag = true;
        }

        if ($generateFlag) {
            $month = $reportedDate->format('m');
            $earned = $leaveType->number_of_days ?? null;
            $previousMonth = ($month == '01') ? 12 : $month - 1;
            if ($leaveType->leave_frequency == 2) {
                $earnedDays = $leaveType->number_of_days ?? null;
                $workingDays = $employee->joined_date->endOfMonth()->format('d') - $employee->joined_date->format('d') + 1;
                $earnedDaysU = $earnedDays * $workingDays / $reportedDateMonthDays;

                $factor = $leaveType->leave_basis == 2 ? 8 : 1;
                $earned = round($earnedDaysU * $factor, 2);
                $earned = round($earned * $percentile / 100, 2);
            }

            $opening = 0;
            if ($month == '01') {
                $previousMonthLeave = $this->model->select(['*'])
                    ->where('employee_id', $employee->id)
                    ->where('fiscal_year_id', $previousYear->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereMonth('reported_date', '=', $previousMonth)
                    ->first();
                $opening = $previousMonthLeave?->balance;
            }

            $inputs = [
                'leave_type_id' => $leaveType->id,
                'fiscal_year_id' => $fiscalYear->id,
                'reported_date' => $reportedDate,
                'opening_balance' => $opening,
                'earned' => $earned,
                'taken' => 0,
                'paid' => 0,
                'lapsed' => 0,
                'balance' => $earned + $opening,
                'remarks' => 'New leave earned.',
            ];

            $employeeLeave = $employee->leaves()->create($inputs);

            $employeeLeave->logs()->create([
                'fiscal_year_id' => $fiscalYear->id,
                'month' => $month,
            ]);
        }
    }

    protected function recurringMonthlyLeaveEarning($leaveType, $employee, $reportedDate, $fiscalYear, $previousYear)
    {
        $reportedDate = Carbon::createFromDate($reportedDate);
        $reportedDateMonthDays = $reportedDate->daysInMonth;
        $percentile = $this->getWorkPercentile($employee, $reportedDate, $fiscalYear);

        if ($leaveType->leave_frequency == 2) {
            $month = $reportedDate->format('m');
            $earnedDays = $leaveType->number_of_days ?? null;
            $previousMonth = ($month == '01') ? 12 : $month - 1;
            $previousMonthLeave = $this->model->select(['*'])
                ->where('employee_id', $employee->id)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereMonth('reported_date', '=', $previousMonth)
                ->first();

            if ($previousMonth == 12) {
                $previousMonthLeave = $this->model->select(['*'])
                    ->where('employee_id', $employee->id)
                    ->where('fiscal_year_id', $previousYear->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereMonth('reported_date', '=', $previousMonth)
                    ->first();
            }
            $opening = $previousMonthLeave?->balance;
            $factor = $leaveType->leave_basis == 2 ? 8 : 1;
            $earned = round($earnedDays * $factor, 2);

            if (date('Y-' . $month) == $employee->joined_date->format('Y-m')) {
                $workingDays = $employee->joined_date->endOfMonth()->format('d') - $employee->joined_date->format('d') + 1;
                $earnedDays = $earnedDays * $workingDays / $reportedDateMonthDays;
                $opening = 0;
                $earned = round($earnedDays * $factor, 2);
            }

            if ($employee->last_working_date) {
                if (date('Y-' . $month) == $employee->last_working_date->format('Y-m')) {
                    $workingDays = $employee->last_working_date->format('d');
                    $earnedDays = $earnedDays * $workingDays / $reportedDateMonthDays;
                    $earned = round($earnedDays * $factor, 2);
                }
            }

            $earned = round($earned * $percentile / 100, 2);
            if ($earned < 0.4) {
                $earned = 0;
            } elseif ($earned >= 0.4 && $earned < 0.9) {
                $earned = 0.5;
            } elseif ($earned >= 0.9 && $earned < 1.4) {
                $earned = 1;
            } elseif ($earned >= 1.4 && $earned <= 1.5) {
                $earned = 1.5;
            }

            $thisMonthLeave = $this->model->select(['*'])
                ->where('employee_id', $employee->id)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereMonth('reported_date', '=', $month)
                ->first();
            $balance = $opening + $earned;

            if ($thisMonthLeave) {
                $inputs = [
                    'opening_balance' => $opening,
                    'earned' => $earned,
                    'taken' => 0,
                    'paid' => 0,
                    'lapsed' => 0,
                    'balance' => $balance,
                    'remarks' => 'New leave earned/reconciled.',
                ];
                $thisMonthLeave->update($inputs);
            } else {
                if ($reportedDate >= $employee->joined_date) {
                    $inputs = [
                        'employee_id' => $employee->id,
                        'fiscal_year_id' => $fiscalYear->id,
                        'leave_type_id' => $leaveType->id,
                        'reported_date' => $reportedDate,
                        'opening_balance' => $opening,
                        'earned' => $earned,
                        'taken' => 0,
                        'paid' => 0,
                        'lapsed' => 0,
                        'balance' => $balance,
                        'remarks' => 'New leave earned.',
                    ];
                    $employeeLeave = $this->model->create($inputs);
                    $employeeLeave->logs()->create([
                        'fiscal_year_id' => $fiscalYear->id,
                        'month' => $month,
                    ]);
                }
            }
        }
    }

    protected function getWorkPercentile($employee, $reportedDate, $fiscalYear)
    {
        return 100;
        /**
        if ($employee->workingHours->count() == 0) {
            return 100;
        }

        $work_percentile = 0;
        $numberOfDaysInMonth = $reportedDate->daysInMonth;
        foreach ($employee->workingHours as $workingHour) {
            $daysInMonth = $this->countDaysInMonthBetweenDates($workingHour->start_date, $workingHour->end_date, $reportedDate->month, $reportedDate->year);
            $work_percentile += (($daysInMonth * $workingHour->work_percentile) / 100) / $numberOfDaysInMonth;
        }

        return $work_percentile * 100;
         */
    }

    protected function countDaysInMonthBetweenDates($startDate, $endDate, $month, $year)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->modify('+1 day');
        $count = 0;
        while ($start < $end) {
            if ((int) $start->format('m') == $month && (int) $start->format('Y') == $year) {
                $count++;
            }
            $start->modify('+1 day');
        }

        return $count;
    }

    public function getLeaveBalances($employeeId)
    {
        return $this->model->from('employee_leaves as el')
            ->select('el.balance', 'lt.title as leave_type_title')
            ->join('lkup_leave_types as lt', 'el.leave_type_id', '=', 'lt.id')
            ->where('el.employee_id', $employeeId)
            ->whereIn('lt.title', ['Annual Leave', 'Sick leave'])
            ->get();
    }
}
