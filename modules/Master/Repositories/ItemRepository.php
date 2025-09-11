<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\InventoryType;
use Modules\Master\Models\Item;
use DB;

class ItemRepository extends Repository
{
    public function __construct(Item $item)
    {
        $this->model = $item;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $item = $this->model->create($inputs);
            $item->units()->sync($inputs['units']);
            DB::commit();
            return $item;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getItems()
    {
        return $this->model->select(['id', 'inventory_category_id', 'title', 'item_code'])
            ->orderBy('title', 'asc')
            ->get();
    }

    public function getActiveItems()
    {
        return $this->model->select(['id', 'inventory_category_id', 'title', 'item_code'])
            ->whereNotNull('activated_at')
            ->orderBy('title', 'asc')
            ->get();
    }

    public function getActiveNonConsumableItems()
    {
        $nonConsumable = app(InventoryType::class)->where('title', 'Non Consumable')->first();

        return $this->model->select(['id', 'inventory_category_id', 'title', 'item_code'])
            ->whereHas('category', function ($q) use ($nonConsumable){
              $q->where('inventory_type_id', $nonConsumable->id);
            })->whereNotNull('activated_at')
            ->orderBy('title', 'asc')
            ->get();
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $item = $this->model->findOrFail($id);
            $item->fill($inputs)->save();
            $item->units()->sync($inputs['units']);
            DB::commit();
            return $item;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
