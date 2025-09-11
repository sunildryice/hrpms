<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\InventoryItem;

class ResetInventoryAssignedQuantity extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reset:inventory:assigned:quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to reset the inventory assigned quantity to 0 for those inventories which doesnt have assigned user.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inventoryItems = InventoryItem::where('assigned_quantity', 1)
            ->whereHas('assets', function ($q) {
                $q->whereNull('assigned_user_id');
                $q->whereHas('latestGoodRequestAsset', function($q){
                    $q->whereNull('assigned_user_id');
                });
            }, '=' , 1)->get();


        DB::beginTransaction();
        try {
            foreach ($inventoryItems as $inventoryItem) {
                $inventoryItem->assigned_quantity = 0;
                $inventoryItem->save();
            }
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $this->info($e);
        }

        $this->info('Assigned Quantity reset');

        
        return Command::SUCCESS;
    }
}
