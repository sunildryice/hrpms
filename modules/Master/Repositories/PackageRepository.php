<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Models\Package;

class PackageRepository extends Repository
{
    public function __construct(Package $package)
    {
        $this->model = $package;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $package = $this->model->create($inputs);
            DB::commit();
            return $package;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $package = $this->model->findOrFail($id);
            $itemCount = $package->packageItems()->count();
            if ($itemCount == 0) {
                $package->activated_at = null;
            } else {
                $inputs['activated_at'] = $package->activated_at ?? date('Y-m-d H:i:s');
            }
            $package->fill($inputs)->save();
            $this->updateTotalAmount($package->id);
            DB::commit();
            return $package;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $package = $this->model->findOrFail($id);
            $package->packageItems()->delete();
            $package->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;

        }
    }

    public function getActivePackages()
    {
        return $this->model->select(['*'])
            ->whereNotNull('activated_at')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function updateTotalAmount($packageId)
    {
        DB::beginTransaction();
        try {
            $package = $this->model->findOrFail($packageId);
            $subTotal = $package->packageItems->sum('total_price');
            $updateInputs = [
                'total_amount' => $subTotal,
            ];
            $package->update($updateInputs);
            DB::commit();
            return $package;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
