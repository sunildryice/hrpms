<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\AdvanceRequest\Models\AdvanceRequest;
use Modules\AdvanceRequest\Models\Settlement;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementCreated;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementForwarded;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementPending;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementSubmitted;

class SendSettlementDueNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:send-settlement-due-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send notification to requester/finance/supervisor(timeline cross)
                             for settlement if tentative settlement date exceeds current date.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for settlement dues.');

        $advanceRequestsWithoutSettlement = AdvanceRequest::where('status_id', config('constant.APPROVED_STATUS'))
                                                        ->doesntHave('advanceSettlement')
                                                        ->get();
        
        if ($advanceRequestsWithoutSettlement->isNotEmpty()) {
            foreach ($advanceRequestsWithoutSettlement as $advanceRequest) {
                $advanceRequest->requester->notify(new AdvanceSettlementPending($advanceRequest));
            }
        }
        

        $advanceSettlements = Settlement::whereNotIn('status_id', [config('constant.APPROVED_STATUS')])
                                        ->whereHas('advanceRequest', function ($q) {
                                            $q->where('settlement_date', '>', now());
                                        })
                                        ->get();

        if ($advanceSettlements->isNotEmpty()) {
            foreach ($advanceSettlements as $advanceSettlement) {
                if (in_array($advanceSettlement->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])) {
                    $advanceSettlement->requester->notify(new AdvanceSettlementCreated($advanceSettlement));
                }
    
                if (in_array($advanceSettlement->status_id, [config('constant.SUBMITTED_STATUS')])) {
                    $advanceSettlement->reviewer->notify(new AdvanceSettlementSubmitted($advanceSettlement));
                }
    
                if (in_array($advanceSettlement->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED2_STATUS')])) {
                    $advanceSettlement->approver->notify(new AdvanceSettlementForwarded($advanceSettlement));
                }
    
            }
        }

        $this->info('Sending notification for settlement dues.');
    }
}
