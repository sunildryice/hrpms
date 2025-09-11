<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableSettlement;
use Modules\Privilege\Repositories\UserRepository;

class SendEmployeeExitPayableSettlementNotification extends Command
{
    private $users;

    public function __construct(UserRepository $users)
    {
        parent::__construct();
        $this->users = $users;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:send-employee-exit-payable-settlement-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send notification to the finance to settle the exit payables of the employee.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for employee exit payable settlement dues.');

        $exitHandOverNotes = ExitHandOverNote::where('last_duty_date', '<', date('Y-m-d'))
                            ->where('status_id', config('constant.APPROVED_STATUS'))
                            ->whereHas('exitInterview', function ($q) {
                                $q->where('status_id', config('constant.APPROVED_STATUS'));
                            })
                            ->whereHas('employeeExitPayable', function ($q) {
                                $q->where('status_id', '!=', config('constant.APPROVED_STATUS'));
                            })
                            ->get();

        $exitPayableSettlementUsers = $this->users->permissionBasedUsersInclusive('approve-exit-payable');


        if ($exitHandOverNotes->isNotEmpty()) {
            foreach ($exitHandOverNotes as $exitHandOverNote) {
                foreach ($exitPayableSettlementUsers as $user) {
                    $user->notify(new ExitPayableSettlement($exitHandOverNote));
                }
            }
        }

        $this->info('Sending notification for employee exit payable settlement dues.');
        $this->info('Completed.');
    }
}
