<?php

namespace Modules\MaintenanceRequest\Repositories;

use App\Repositories\Repository;
use Modules\MaintenanceRequest\Models\MaintenanceRequestItem;

use DB;

class MaintenanceRequestItemRepository extends Repository
{
    public function __construct(
        MaintenanceRequestRepository $maintenanceRequests,
        MaintenanceRequestItem       $maintenanceRequestItem
    ) {
        $this->maintenanceRequests = $maintenanceRequests;
        $this->model = $maintenanceRequestItem;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequestItem = $this->model->create($inputs);
            $this->maintenanceRequests->updateTotalAmount($maintenanceRequestItem->maintenance_id);
            DB::commit();
            return $maintenanceRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequestItem = $this->model->findOrFail($id);
            $maintenanceRequestItem->delete();
            $this->maintenanceRequests->updateTotalAmount($maintenanceRequestItem->maintenance_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequestItem = $this->model->findOrFail($id);
            $maintenanceRequestItem->fill($inputs)->save();
            //            $maintenanceRequestItem = $this->model->findOrFail($id);
            $this->maintenanceRequests->updateTotalAmount($maintenanceRequestItem->maintenance_id);
            DB::commit();
            return $maintenanceRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }
}
