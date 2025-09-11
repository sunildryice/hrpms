<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;

class SubmitWorkLogExitEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:submit:work:log:exit:employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit work log and attendance for exit employee.';

    /**
     * @param AttendanceRepository $attendances
     * @param EmployeeRepository $employees
     * @param ExitHandOverNoteRepository $exitHandovers
     * @param WorkPlanRepository $workPlans
     */
    public function __construct(
        AttendanceRepository $attendances,
        EmployeeRepository   $employees,
        ExitHandOverNoteRepository  $exitHandovers,
        WorkPlanRepository $workPlans
    )
    {
        parent::__construct();
        $this->attendances = $attendances;
        $this->employees = $employees;
        $this->exitHandovers = $exitHandovers;
        $this->workPlans = $workPlans;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting all employee exit handovers.');
        $employeeExits = $this->exitHandovers->with(['employee'])->get();
        $employeeIds = [];
        foreach($employeeExits as $employeeExit)
        {
            if(date('Y-m-d') > $employeeExit->last_duty_date){
                $employeeIds[] = $employeeExit->employee_id;
            }
        }
        $this->info('Getting all work plans of exit employee.');
        $workPlans = $this->workPlans->select(['*'])
            ->where('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            ->whereIn('employee_id', $employeeIds)->get();
        foreach($workPlans as $workPlan)
        {
            $inputs = [
                'btn'=>'submit',
                'user_id'=>2,
                'original_user_id'=>2,
            ];
            $plan = $this->workPlans->submit($workPlan->id, $inputs);
            $this->info('Work plan '. $plan->id .' of '. $workPlan->getEmployeeName() .' submitted.');
        }

        $this->info('Getting all attendance of exit employee.');
        $attendances = $this->attendances->with(['employee'])
            ->where('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            ->whereIn('employee_id', $employeeIds)->get();
        foreach($attendances as $attendance){
            $inputs = [
                'btn'=>'submit',
                'remarks'=>'',
                'user_id'=>2,
                'original_user_id'=>2,
                'reviewer_id'=>$attendance->employee->supervisor_id,
                'status_id'=>config('constant.SUBMITTED_STATUS'),
            ];
            $attendance = $this->attendances->submit($attendance->id, $inputs);
            $this->info('Attendance '. $attendance->id .' of '. $attendance->getRequester() .' submitted.');
        }
    }
}
