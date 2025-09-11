<?php

namespace Modules\GoodRequest\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Models\GoodRequest;
use Modules\GoodRequest\Models\GoodRequestItem;
use Modules\Inventory\Models\InventoryItem;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;

class GoodRequestRepository extends Repository
{
    private $assets;

    private $fiscalYears;

    private $goodRequestItems;

    private $inventoryItems;

    /**
     * Constructor
     */
    public function __construct(
        AssetRepository $assets,
        FiscalYearRepository $fiscalYears,
        GoodRequest $goodRequest,
        GoodRequestItem $goodRequestItems,
        InventoryItemRepository $inventoryItems,
        EmployeeRepository $employees,
    ) {
        $this->assets = $assets;
        $this->fiscalYears = $fiscalYears;
        $this->model = $goodRequest;
        $this->goodRequestItems = $goodRequestItems;
        $this->inventoryItems = $inventoryItems;
        $this->employees = $employees;
    }


    /**
     * Get a list of approved good requests.
     *
     * @return mixed
     */
    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function generateGoodRequestNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'good_request_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('good_request_number') + 1;

        return $max;
    }

    public function assign($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->find($id);
            $goodRequest->update($inputs);
            $goodRequest->logs()->create($inputs);
            if ($inputs['status_id'] == config('constant.ASSIGNED_STATUS')) {

                foreach ($inputs['assigned_inventory_item_id'] as $goodRequestItemId => $assignedInventoryItemId) {
                    $assignItemInputs = [];
                    $goodRequestItem = $this->goodRequestItems->find($goodRequestItemId);
                    $inventoryItem = $this->inventoryItems->find($assignedInventoryItemId);
                    $assignItemInputs['assigned_inventory_item_id'] = $inventoryItem->id;
                    $assignItemInputs['assigned_item_id'] = $inventoryItem->item_id;
                    $assignItemInputs['assigned_unit_id'] = $inventoryItem->unit_id;
                    $assignItemInputs['inventory_category_id'] = $inventoryItem->category_id;
                    if ($inventoryItem->item->category->inventoryType->title != 'Consumable') {
                        $assignItemInputs['assigned_quantity'] = count($inputs['assigned_asset_ids'][$goodRequestItemId]);
                        foreach ($inputs['assigned_asset_ids'][$goodRequestItemId] as $goodRequestAssetId) {
                            $assetInputs = [
                                'good_request_id' => $goodRequestItem->good_request_id,
                                'good_request_item_id' => $goodRequestItem->id,
                                'assign_asset_id' => $goodRequestAssetId,
                                'status' => config('constant.ASSET_ASSIGNED'),
                                'handover_status_id' => config('constant.CREATED_STATUS'),

                                'assigned_user_id' => $goodRequest->requester->id,
                                'assigned_district_id' => $goodRequest->requester->employee->latestTenure?->duty_station_id,
                                'assigned_office_id' => $goodRequest->requester->employee->latestTenure?->office_id,
                                'assigned_department_id' => $goodRequest->requester->employee->latestTenure?->department_id,
                                'assigned_on' => now()->format('Y-m-d'),
                            ];
                            $goodRequestAsset = $goodRequestItem->goodRequestAssets()->create($assetInputs);
                            $goodRequestAsset->asset->update([
                                'assigned_user_id' => $assetInputs['assigned_user_id'],
                                'assigned_office_id' => $assetInputs['assigned_office_id'],
                                'assigned_department_id' => $assetInputs['assigned_department_id'],
                                'status' => $assetInputs['status'],
                            ]);
                            $goodRequestAsset->asset->inventoryItem->increment('assigned_quantity');
                            $assetInputs['good_request_asset_id'] = $goodRequestAsset->id;
                            $assetInputs['remarks'] = 'Asset id assigned';
                            $assetInputs['created_by'] = $assetInputs['updated_by'] = $inputs['user_id'];
                            $goodRequestAsset->asset->assetAssignLogs()->create($assetInputs);

                        }
                    } else {
                        $assignedQuantity = intval($inputs['assigned_quantity'][$goodRequestItemId]);
                        $assignItemInputs['assigned_quantity'] = $assignedQuantity;
                        $inventoryItem->increment('assigned_quantity', $assignedQuantity);
                    }
                    $goodRequestItem->update($assignItemInputs);
                }
            }

            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $goodRequest = $this->model->create($inputs);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function storeDirectDispatch($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['is_direct_dispatch'] = '1';
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();
            $inputs['prefix'] = 'GR';
            $inputs['good_request_number'] = $this->generateGoodRequestNumber($inputs['fiscal_year_id']);
            $inputs['logistic_officer_id'] = $inputs['created_by'];

            $goodRequest = $this->model->create($inputs);

            $goodRequest->logs()->create([
                'user_id' => $inputs['created_by'],
                'original_user_id' => $inputs['original_user_id'],
                'log_remarks' => 'Direct dispatch good request has been submitted.',
                'status_id' => $inputs['status_id'],
            ]);

            $inventoryItem = $this->inventoryItems->find($inputs['assigned_inventory_item_id']);

            $goodRequestItem = $goodRequest->goodRequestItems()->create([
                'item_name' => $inventoryItem->getItemName(),
                'unit_id' => $inventoryItem->unit_id,
                'quantity' => $inputs['quantity'],
                'specification' => $inventoryItem->specification,
                'inventory_category_id' => $inventoryItem->category_id,
                'assigned_inventory_item_id' => $inventoryItem->id,
                'assigned_item_id' => $inventoryItem->item_id,
                'assigned_unit_id' => $inventoryItem->unit_id,
            ]);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function approveDirectDispatch($id, $inputs)
    {
        $goodRequest = $this->model->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                foreach ($inputs['assigned_quantity'] as $key => $quantity) {
                    $goodRequestItem = $this->goodRequestItems->find($key);
                    $inventoryItem = $goodRequestItem->assignedInventoryItem;
                    $goodRequestItem->update([
                        'assigned_quantity' => $quantity,
                        'assigned_specification' => $inventoryItem->specification,
                    ]);
                    $inventoryItem->update([
                        'assigned_quantity' => ($inventoryItem->assigned_quantity + $quantity),
                    ]);
                }

                $goodRequest->update([
                    'status_id' => config('constant.APPROVED_STATUS'),
                ]);
                $goodRequest->logs()->create([
                    'user_id' => $inputs['created_by'],
                    'original_user_id' => $inputs['original_user_id'],
                    'log_remarks' => $inputs['log_remarks'],
                    'status_id' => $inputs['status_id'],
                ]);

            } else {
                $goodRequest->update([
                    'status_id' => $inputs['status_id'],
                ]);
                $goodRequest->logs()->create([
                    'user_id' => $inputs['created_by'],
                    'original_user_id' => $inputs['original_user_id'],
                    'log_remarks' => $inputs['log_remarks'],
                    'status_id' => $inputs['status_id'],
                ]);
            }
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $goodRequest = $this->model->findOrFail($id);
            $goodRequest->goodRequestItems()->delete();
            $goodRequest->logs()->delete();
            $goodRequest->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (! $goodRequest->good_request_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'GR';
                $inputs['good_request_number'] = $this->generateGoodRequestNumber($fiscalYear->id);
            }
            $goodRequest->update($inputs);
            $goodRequest->logs()->create($inputs);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->find($id);
            $goodRequest->update($inputs);
            $goodRequest->logs()->create($inputs);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->find($id);
            $goodRequest->update($inputs);
            $goodRequest->logs()->create($inputs);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->find($id);
            $goodRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Good request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $goodRequest = $this->forward($goodRequest->id, $forwardInputs);
            }
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function storeDirectAssign($assetId, $inputs)
    {
        DB::beginTransaction();
        try {
            $asset = $this->assets->find($assetId);
            $employee = $this->employees->find($inputs['employee_id']);

            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['is_direct_assign'] = '1';
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();
            $inputs['prefix'] = 'GR';
            $inputs['good_request_number'] = $this->generateGoodRequestNumber($inputs['fiscal_year_id']);
            $inputs['receiver_id'] = $employee->getUserId();
            $inputs['is_direct_assign'] = true;
            $inputs['logistic_officer_id'] = $inputs['created_by'];
            $goodRequest = $this->model->create($inputs);

            $goodRequest->logs()->create([
                'user_id' => $inputs['created_by'],
                'original_user_id' => $inputs['original_user_id'],
                'log_remarks' => 'Direct assign good request has been submitted.',
                'status_id' => $inputs['status_id'],
            ]);

            $goodRequestItem = $goodRequest->goodRequestItems()->create([
                'item_name' => $asset->inventoryItem->getItemName(),
                'unit_id' => $asset->inventoryItem->unit_id,
                'quantity' => 1,
                'inventory_category_id' => $asset->inventoryItem->category_id,
                'assigned_inventory_item_id' => $asset->inventory_item_id,
                'assigned_item_id' => $asset->inventory_item_id,
                'assigned_unit_id' => $asset->inventoryItem->unit_id,
                'specification' => $asset->inventoryItem->specification,
            ]);

            $goodRequestItem->goodRequestAssets()->create([
                'assign_asset_id' => $asset->id,
                'good_request_id' => $goodRequest->id,
                'room_number' => $inputs['room_number'],
                'handover_status_id' => 0,
            ]);

            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
            DB::rollback();

            return false;
        }
    }

    public function approveDirectAssign($goodRequestId, $inputs)
    {
        $goodRequest = $this->model->findOrFail($goodRequestId);
        DB::beginTransaction();
        try {
            $goodRequestAsset = $goodRequest->goodRequestAssets()->first();
            $goodRequestItem = $goodRequest->goodRequestItems()->first();
            $asset = $goodRequestAsset->asset;
            $receiver = $goodRequest->receiver->employee;
            if ($inputs['status_id'] == config('constant.ASSIGNED_STATUS')) {
                $goodRequest->update($inputs);
                $goodRequest->logs()->create([
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                    'log_remarks' => $inputs['log_remarks'],
                    'status_id' => $inputs['status_id'],
                ]);
                $goodRequestItem->update([
                    'assigned_quantity' => 1,
                    'assigned_specification' => $asset->inventoryItem->specification,
                ]);
                $asset->update([
                    'assigned_department_id' => $receiver->department_id,
                    'assigned_office_id' => $receiver->office_id,
                    'assigned_user_id' => $goodRequest->receiver_id,
                    'status' => '2',
                    'room_number' => $goodRequestAsset->room_number,
                ]);
                $goodRequestAsset->update([
                    'asset_condition' => $asset->latestConditionLog->condition->title,
                    'status' => 2,
                    'assigned_user_id' => $goodRequest->receiver_id,
                    'assigned_district_id' => null,
                    'assigned_office_id' => $receiver->office_id,
                    'assigned_department_id' => $receiver->department_id,
                    'assigned_on' => date('Y-m-d H:i:s'),
                ]);
                $goodRequestAsset->asset->inventoryItem->increment('assigned_quantity');
                $asset->assetAssignLogs()->create([
                    'assigned_office_id' => $receiver->office_id,
                    'assigned_department_id' => $receiver->department_id,
                    'assigned_user_id' => $receiver->getUserId(),
                    'condition_id' => $asset->latestConditionLog->condition_id,
                    'remarks' => 'Asset Direct Assigned',
                    'created_by' => $inputs['updated_by'],
                ]);
            } else {
                $goodRequest->update([
                    'status_id' => $inputs['status_id'],
                ]);
                $goodRequest->logs()->create([
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                    'log_remarks' => $inputs['log_remarks'],
                    'status_id' => $inputs['status_id'],
                ]);
                $goodRequest->goodRequestAssets()->delete();
            }
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function receive($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequest = $this->model->findOrFail($id);
            $goodRequest->update([
                'received_at' => date('Y-m-d H:i:s'),
                'receiver_note' => $inputs['log_remarks'],
            ]);
            $goodRequest->logs()->create([
                'user_id' => $inputs['created_by'],
                'original_user_id' => $inputs['original_user_id'],
                'log_remarks' => $inputs['log_remarks'] ?? 'Good request has been received.',
                'status_id' => config('constant.RECEIVED_STATUS'),
            ]);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function storeDispatchBulk($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['is_direct_dispatch'] = '1';
            $inputs['status_id'] = config('constant.CREATED_STATUS');
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();

            if ($inputs['btn'] == 'submit') {
                $inputs['prefix'] = 'GR';
                $inputs['good_request_number'] = $this->generateGoodRequestNumber($inputs['fiscal_year_id']);
                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            }
            $goodRequest = $this->model->create($inputs);
            if (array_key_exists('employee_ids', $inputs)) {
                $goodRequest->employees()->sync($inputs['employee_ids']);
            } else {
                $goodRequest->employees()->sync([]);
            }

            if ($inputs['btn'] == 'submit') {
                $inputs['log_remarks'] = 'Direct dispatch good request has been submitted.';
                $goodRequest->logs()->create($inputs);
            }

            foreach ($inputs['dispatch_item']['assigned_inventory_item_id'] as $index => $inventory_item_id) {
                $inventoryItem = $this->inventoryItems->find($inventory_item_id);
                $goodRequest->goodRequestItems()->create([
                    'item_name' => $inventoryItem->getItemName(),
                    'unit_id' => $inventoryItem->unit_id,
                    'quantity' => $inputs['dispatch_item']['assigned_quantity'][$index],
                    'specification' => $inventoryItem->specification,
                    'inventory_category_id' => $inventoryItem->category_id,
                    'assigned_inventory_item_id' => $inventoryItem->id,
                    'assigned_item_id' => $inventoryItem->item_id,
                    'assigned_unit_id' => $inventoryItem->unit_id,
                    // 'assigned_quantity'  => $inputs['dispatch_item']['assigned_quantity'][$inventory_item_id],
                ]);
            }

            // dd($goodRequest->toArray(), $goodRequest->goodRequestItems->toArray());

            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);

            return false;
        }
    }

    public function updateDispatchBulk($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['is_direct_dispatch'] = '1';
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();
            $inputs['prefix'] = 'GR';
            $inputs['good_request_number'] = $this->generateGoodRequestNumber($inputs['fiscal_year_id']);

            $goodRequest = $this->model->create($inputs);

            $goodRequest->logs()->create([
                'user_id' => $inputs['created_by'],
                'original_user_id' => $inputs['original_user_id'],
                'log_remarks' => 'Direct dispatch good request has been submitted.',
                'status_id' => $inputs['status_id'],
            ]);

            $inventoryItem = $this->inventoryItems->find($inputs['assigned_inventory_item_id']);

            $goodRequestItem = $goodRequest->goodRequestItems()->create([
                'item_name' => $inventoryItem->getItemName(),
                'unit_id' => $inventoryItem->unit_id,
                'quantity' => $inputs['quantity'],
                'specification' => $inventoryItem->specification,
                'inventory_category_id' => $inventoryItem->category_id,
                'assigned_inventory_item_id' => $inventoryItem->id,
                'assigned_item_id' => $inventoryItem->item_id,
                'assigned_unit_id' => $inventoryItem->unit_id,
            ]);
            DB::commit();

            return $goodRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }

    }
}
