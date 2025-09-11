<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Models\PackageItem;
use Modules\Master\Repositories\PackageRepository;

class PackageItemRepository extends Repository
{
    protected $packages;
    public function __construct(PackageItem $packageItem, PackageRepository $packages)
    {
        $this->model = $packageItem;
        $this->packages = $packages;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $packageItem = $this->model->create($inputs);
            $this->packages->updateTotalAmount($packageItem->package_id);
            DB::commit();
            return $packageItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $packageItem = $this->model->findOrFail($id);
            $packageItem->delete();
            $this->packages->updateTotalAmount($packageItem->package_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $packageItem = $this->model->findOrFail($id);
            $packageItem->fill($data)->save();
            $this->packages->updateTotalAmount($packageItem->package_id);
            DB::commit();
            return $packageItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
