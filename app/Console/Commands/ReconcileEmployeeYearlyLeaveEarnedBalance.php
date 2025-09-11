<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Repositories\OfficeRepository;

class ReconcileEmployeeYearlyLeaveEarnedBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reconcile-employee-yearly-leave-earned-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile employee earned balance for yearly leaves';

    /**
     * @param LeaveRepository $leaves
     */
    public function __construct(
        LeaveRepository     $leaves,
        LeaveTypeRepository $leaveTypes,
        OfficeRepository    $offices
    )
    {
        parent::__construct();
        $this->leaves = $leaves;
        $this->leaveTypes = $leaveTypes;
        $this->offices = $offices;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leaveTypes = $this->leaveTypes->getActivated()->reject(function ($leave) {
            return $leave->leave_frequency == 2;
        });
        $leaveTypeIds = $leaveTypes->pluck('id')->toArray();
        $year =  date('Y');

        $this->info('Getting all employee Leaves');
        $employeeLeaves = $this->leaves->select(['*'])
            ->whereYear('reported_date', $year)
            ->whereIn('leave_type_id', $leaveTypeIds)
            ->get();

        foreach ($employeeLeaves as $employeeLeave) {
            $this->info($employeeLeave->id .' | '. $employeeLeave->leave_type_id .' | '. $employeeLeave->earned);
            $leaveType = $this->leaveTypes->find($employeeLeave->leave_type_id);
            $this->info($leaveType->title .' | '. $leaveType->number_of_days);
            $balance = $leaveType->id=18 ? $leaveType->number_of_days : $leaveType->number_of_days-$employeeLeave->taken - $employeeLeave->paid;

            if(in_array($employeeLeave->id, [6153,6028,6014,5985])){
                $balance = $employeeLeave->opening_balance + $leaveType->number_of_days;
            }
            $employeeLeave->update([
                'earned' => $leaveType->number_of_days,
                'balance'=> $balance,
            ]);
        }
        $this->info('All leave opening are updated.');
    }
}
