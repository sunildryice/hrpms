<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Inventory\Models\InventoryItem;

class RecalcAssignedQty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:recalculate:assigned:qty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $consumableInventoryItems = InventoryItem::query()->withWhereHas('item.category.inventoryType', function ($query) {
            $query->where('title', 'Consumable');
        })->get();

        foreach ($consumableInventoryItems as $inventoryItem) {
            /** @var InventoryItem $inventoryItem */
            $inventoryItem->updateAssignedQuantity();
            $this->info("Updated assigned quantity for inventory item: {$inventoryItem->id} - {$inventoryItem->item->title}");
        }

        return Command::SUCCESS;
    }
}
