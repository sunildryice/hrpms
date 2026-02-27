<?php

namespace Modules\Inventory\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\Asset;

class AssetRepository extends Repository
{
    public function __construct(Asset $asset)
    {
        $this->model = $asset;
    }

    public function getAssetNumber($prefix)
    {
        $max = $this->model->where('prefix', $prefix)
            ->max('asset_number') + 1;

        return $max;
    }

    public function generateAssets($inventory)
    {
        DB::beginTransaction();
        try {
            $prefix = strtoupper(substr($inventory->item->item_code, 0, 3));
            if (isset($inventory->office_id)) {
                $prefix = strtoupper(substr($inventory->item->item_code, 0, 3));
            }
            $year = $inventory->purchase_date ? Carbon::create($inventory->purchase_date)->format('Y') : date('Y');
            for ($i = 1; $i <= $inventory->quantity; $i++) {
                $asset = $this->model->create([
                    'inventory_item_id' => $inventory->id,
                    'prefix' => $prefix,
                    'year' => $year,
                    'asset_number' => $this->getAssetNumber($prefix),
                    'assigned_office_id' => $inventory->office_id,
                    'status' => config('constant.ASSET_NEW'),
                ]);
                $asset->logs()->create([
                    'user_id' => $inventory->created_by,
                    'remarks' => 'Asset is created.',
                ]);
                $asset->assetConditionLogs()->create([
                    'condition_id' => config('constant.ASSET_NEW'),
                    'description' => 'New asset created.',
                ]);
            }
            DB::commit();

            return $inventory;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getDisposableAssets($officeId, $disposedAssets = [])
    {
        return $this->model
            ->whereDoesntHave('disposition', function ($q) {
                $q->whereHas('dispositionRequest', function ($q) {
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                });
            })
            ->where(function ($q) use ($officeId) {
                $q->where('assigned_office_id', $officeId);
                $q->orWhereHas('inventoryItem', function ($q) use ($officeId) {
                    $q->where('office_id', $officeId);
                });
            })
            ->whereNotIn('id', $disposedAssets)
            ->get();
    }

    public function getAssetOnStore(): Collection
    {
        $authUser = Auth::user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        if ($currentOffice) {
            return $this->model->with([
                'assignedTo',
                'assignedTo.employee.latestTenure.office',
                'inventoryItem',
                'inventoryItem.office',
                'latestConditionLog',
                'latestConditionLog.condition',
            ])->where(function ($q) {
                $q->whereHas('latestGoodRequestAsset', function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('assigned_user_id');
                        $q->whereHas('goodRequest', function ($q) {
                            $q->where('status_id', config('constant.REJECTED_STATUS'));
                        });
                    });
                    $query->orWhere(function ($q) {
                        $q->whereHas('goodRequest');
                        $q->where('handover_status_id', config('constant.APPROVED_STATUS'));
                    });
                });
                $q->orWhereNull('assigned_user_id');
            })
                ->whereDoesntHave('dispositionRequest', function ($query) {
                    $query->where('status_id', config('constant.APPROVED_STATUS'));
                })
                // ->where(function ($q) use ($accessibleOfficeIds) {
                //     $q->whereIn('assigned_office_id', $accessibleOfficeIds);
                // })
                ->select(['*'])->orderBy('created_at', 'desc')->get();

        }

        return collect();
    }

    public function getAssignedAsset(): Collection
    {
        $authUser = Auth::user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        if ($currentOffice) {
            return $this->model->with([
                'assignedTo',
                'assignedTo.employee',
                'assignedTo.employee.latestTenure.office',
                'inventoryItem',
                'inventoryItem.office',
                'latestConditionLog',
                'latestConditionLog.condition',
            ])->select(['*'])
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereNotNull('assigned_user_id');
                        // $q->where(function ($q) use ($accessibleOfficeIds) {
                        //     $q->whereIn('assigned_office_id', $accessibleOfficeIds);
                        //     $q->orWhereHas('assignedTo', function ($q) use ($accessibleOfficeIds) {
                        //         $q->whereHas('employee', function ($q) use ($accessibleOfficeIds) {
                        //             $q->whereIn('office_id', $accessibleOfficeIds);
                        //         });
                        //     });
                        // });
                    })
                        ->orWhere(function ($q) {
                            $q->whereHas('latestGoodRequestAsset', function ($q) {
                                $q->whereNotNull('assigned_user_id');
                                $q->where('handover_status_id', '<>', config('constant.APPROVED_STATUS'));
                                // $q->where(function ($q) use ($accessibleOfficeIds) {
                                //     $q->whereIn('assigned_office_id', $accessibleOfficeIds);
                                //     $q->orWhereHas('assignedTo', function ($q) use ($accessibleOfficeIds) {
                                //         $q->whereHas('employee', function ($q) use ($accessibleOfficeIds) {
                                //             $q->whereIn('office_id', $accessibleOfficeIds);
                                //         });
                                //     });
                                // });
                            });
                        });
                })
                ->whereDoesntHave('dispositionRequest', function ($query) {
                    $query->where('status_id', config('constant.APPROVED_STATUS'));
                })
                ->orderBy('created_at', 'desc')->get();
        }

        return collect();
    }

    public function recover($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $asset = $this->model->findOrFail($id);
            if ($asset->latestGoodRequestAsset->exists() && $asset->latestGoodRequestAsset->handover_status_id != config('constant.APPROVED_STATUS')) {
                $asset->latestGoodRequestAsset()->update(['handover_status_id' => config('constant.APPROVED_STATUS')]);
                $asset->latestGoodRequestAsset->logs()->create([
                    'user_id' => auth()->id(),
                    'log_remarks' => 'Asset Reclaim by logistic',
                    'handover_status_id' => config('constant.APPROVED_STATUS'),
                ]);
                $asset->inventoryItem->decrement('assigned_quantity');
            }

            $inputs['assigned_user_id'] = null;
            $inputs['assigned_department_id'] = null;
            $inputs['status'] = config('constant.ASSET_ON_STORE');
            $asset->fill($inputs)->save();

            $asset->assetAssignLogs()->create([
                'assigned_office_id' => $asset->assigned_office_id,
                'assigned_department_id' => $asset->assigned_department_id,
                'condition_id' => $asset->latestConditionLog->condition_id,
                'remarks' => 'Asset Reclaim by logistic',
                'created_by' => auth()->id(),
            ]);
            // dd($asset->inventoryItem, Gate::inspect('update', $asset->inventoryItem));

            DB::commit();

            return $asset;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);

            return false;
        }
    }
}
