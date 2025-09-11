<?php

namespace Modules\Inventory\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Inventory\Models\InventoryItem;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\ConditionRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ExecutionRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Supplier\Repositories\SupplierRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Inventory\Repositories\AssetConditionLogRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;

class InventoryImport implements WithHeadingRow, ToCollection
{
    public function __construct()
    {
        $this->assets       = app(AssetRepository::class);
        $this->assetConditionLogs = app(AssetConditionLogRepository::class);
        $this->conditions   = app(ConditionRepository::class);
        $this->employees    = app(EmployeeRepository::class);
        $this->executions   = app(ExecutionRepository::class);
        $this->goodRequestAssets = app(GoodRequestAssetRepository::class);
        $this->inventories  = app(InventoryItemRepository::class);
        $this->items        = app(ItemRepository::class);
        $this->offices      = app(OfficeRepository::class);
        $this->suppliers    = app(SupplierRepository::class);
        $this->units        = app(UnitRepository::class);
        $this->activityCode = app(ActivityCodeRepository::class);
        $this->accountCode  = app(AccountCodeRepository::class);
        $this->donorCode    = app(DonorCodeRepository::class);
    }

    /**
     * Summary of collection
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        ini_set('memory_limit', '-1');
        ini_set('set_time_limit', '120');

        foreach ($rows as $index => $row) {
            $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
            $purchaseDate = NULL;
            if (preg_match($datePattern, $row['purchase_date_yyyy_mm_dd'])) {
                $purchaseDate = $row['purchase_date_yyyy_mm_dd'];
                $year = date('Y', strtotime($purchaseDate));
            } else {
                if(!empty($row['purchase_date_yyyy_mm_dd'])){
                    try{
                        $dateRaw = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date_yyyy_mm_dd']);
                        $purchaseDate =$dateRaw->format('Y-m-d');
                        $year = $dateRaw->format('Y');
                    }catch(\TypeError $e){
                        dd($row);
                    }
                }
            }

            $inputs   = [
                'item'              => isset($row['item']) ? $row['item'] : null,
                'item_code'         => isset($row['item_code']) ? $row['item_code'] : null,
                'item_name'         => isset($row['item_name']) ? $row['item_name'] : null,
                'description'       => isset($row['description']) ? $row['description'] : null,
                'serial_number'     => isset($row['serial_number']) ? $row['serial_number'] : null,
                'unit_price'        => isset($row['unit_price']) ? (float)$row['unit_price'] : 0,
                'quantity'          => isset($row['quantity']) ? $row['quantity'] : 1,
                'measurement_unit'  => isset($row['measurement_unit']) ? $row['measurement_unit'] : 'Numbers',
                'purchase_date'     => $purchaseDate,
                'execution'         => isset($row['execution']) ? $row['execution'] : null,
                'voucher_number'    => isset($row['voucher_number']) ? $row['voucher_number'] : null,
                'asset_code'        => isset($row['asset_code']) ? $row['asset_code'] : null,
                'office_code'       => isset($row['office_code']) ? $row['office_code'] : null,
                'employee_code'     => isset($row['employee_code']) ? (int)$row['employee_code'] : null,
                'location_office_code' => isset($row['location_office_code']) ? $row['location_office_code'] : null,
                'condition'         => isset($row['condition']) ? $row['condition'] : null,
                'supplier'          => isset($row['supplier']) ? $row['supplier'] : null,
                'remarks'           => isset($row['remarks']) ? $row['remarks'] : null,
                'room_number'           => isset($row['room_number']) ? $row['room_number'] : null,
                'asset_disposed'    => isset($row['asset_disposed']) ? ($row['asset_disposed'] == 'yes' ? true : false) : false,
                'vat_amount'        => isset($row['vat_amount']) ? $row['vat_amount'] : 0,
                'activity_code'     => isset($row['activity_code']) ? $row['activity_code'] : null,
                'account_code'      => isset($row['account_code']) ? $row['account_code'] : null,
                'donor_code'        => isset($row['donor_code']) ? $row['donor_code'] : null,
            ];

            $authUser = auth()->user();
            $office = $this->offices->where('office_code', '=', $inputs['office_code'])->first();
            $locationOffice = $this->offices->where('office_code', '=', $inputs['location_office_code'])->first();
            $execution = $this->executions->where('title', '=', $inputs['execution'])->first();
            $condition = $this->conditions->where('title', '=', $inputs['condition'])->first();
            $supplier = $this->suppliers->where('supplier_name', '=', $inputs['supplier'])->first();
            $item = $this->items->where('item_code', '=', $inputs['item_code'])->first();
            $unit = $this->units->where('title', '=', $inputs['measurement_unit'])->first();
            $activityCode = $this->activityCode->where('title', '=', $inputs['activity_code'])->first();
            $accountCode = $this->accountCode->where('title', '=', $inputs['account_code'])->first();
            $donorCode = $this->donorCode->where('title', '=', $inputs['donor_code'])->first();
            if ($item == null) {
                continue;
            }
            $employee = $this->employees->where('employee_code', '=', $inputs['employee_code'])->first();

            $inputs_inventory = [
                'office_id'             => $office?->id,
                'office_code'             => $office ? $office->office_code : 'KTM',
                'category_id'           => $item?->inventory_category_id,
                'item_id'               => $item?->id,
                'item_code'             => $inputs['item_code'],
                'unit_id'               => $unit?->id,
                'distribution_type_id'  => 1,   // Office Use => 1 AND Distribution => 2
                'item_name'             => $inputs['item_name'],
                'specification'         => $inputs['description'],
                'purchase_date'         => $purchaseDate,
                'quantity'              => $inputs['quantity'] ?: 1,
                'unit_price'            => $inputs['unit_price'],
                'total_price'           => $inputs['unit_price'] * $inputs['quantity'] ?: 1,
                'created_by'            => $authUser->id,
                'updated_by'            => $authUser->id,
                'asset_flag'            => true,
                'asset_code'            => $inputs['asset_code'],
                'assigned_user_id'      => $employee?->user?->id ?: null,
                'assigned_department_id'=> (!empty($employee->department_id)?$employee->department_id:null),
                'assigned_office_id'    => $locationOffice?->id ?: null,
                'asset_disposed'        => $inputs['asset_disposed'],
                'voucher_number'        => $inputs['voucher_number'],
                'execution_id'          => $execution?->id ?: null,
                'supplier_id'           => $supplier?->id ?: null,
                'serial_number'         => $inputs['serial_number'],
                'condition_id'          => $condition?->id ?: null,
                'asset_condition'       => $inputs['condition'],
                'remarks'               => $inputs['remarks'],
                'room_number'           => $inputs['room_number'],
                'purchase_year'         => $year,
                'vat_amount'            => $inputs['vat_amount'],
                'activity_code_id'      => $activityCode?->id ?: null,
                'account_code_id'       => $accountCode?->id ?: null,
                'donor_code_id'         => $donorCode?->id ?: null,
            ];

            $inputs_inventory['assigned_quantity'] = $this->generateAssetStatus($inputs_inventory) == config('constant.ASSET_ASSIGNED') ? 1 : 0;

            $this->createInventory($inputs_inventory);
        }
    }

    private function createInventory($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['total_amount'] = $inputs['total_price'] + $inputs['vat_amount'];
            $inventory = InventoryItem::create($inputs);
            $inventory['office_code'] = $inventory->office->office_code;
            if($inputs['asset_flag']) {
                $this->generateAssets($inventory, $inputs);
            }
            DB::commit();
            return $inventory;
        } catch (QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    private function generateAssets($inventory, $inputs)
    {
        DB::beginTransaction();
        try {
            $prefix = 'OHW/'.strtoupper($inventory['office_code']).'/'.strtoupper($inputs['item_code']);
            $year = date('Y');
            if(isset($inputs['purchase_year'])){
                $year = $inputs['purchase_year'];
            }
            $numberPattern = '/^[A-Z]{3}\/[A-Z]{3}\/[A-Z]{1,5}\/(\d{3})\/\d{4}$/';
            $assetNumber = 0;
            if(preg_match($numberPattern, $inputs['asset_code'], $m)){
                $assetNumber = $m[1];
                $assetNumber = preg_replace('/[^0-9]/', '', $assetNumber);
            }
            $assetNumber = ltrim($assetNumber, 0);
            $asset = $this->assets->create([
                'inventory_item_id'         => $inventory->id,
                'prefix'                    => $prefix,
                'year'                      => $year,
                'asset_number'              => $assetNumber ?: $this->assets->getAssetNumber($prefix, $year),
                'old_asset_code'            => $inputs['asset_code'],
                'assigned_office_id'        => $inputs['assigned_office_id'],
                'assigned_department_id'    => $inputs['assigned_department_id'],
                'assigned_user_id'          => $inputs['assigned_user_id'],
                'status'                    => $this->generateAssetStatus($inputs),
                'serial_number'             => $inputs['serial_number'],
                'condition_id'              => $inputs['condition_id'],
                'remarks'                   => $inputs['remarks']
            ]);
            $asset->assetAssignLogs()->create([
                'assigned_office_id'        => $inputs['assigned_office_id'],
                'assigned_department_id'    => $inputs['assigned_department_id'],
                'assigned_user_id'          => $inputs['assigned_user_id'],
                'condition_id'          => $inputs['condition_id'],
                'remarks'          => $inputs['room_number'],
                'created_by'   => $inventory['created_by']
            ]);
            $asset->logs()->create([
                'user_id'       => $inventory->created_by,
                'log_remarks'   => $this->generateAssetLogRemarks($inputs),
                'status'        => $this->generateAssetStatus($inputs)
            ]);
            $asset->assetConditionLogs()->create([
                'condition_id' => $inputs['condition_id'],
                'description' => $inputs['remarks']
            ]);

            $goodRequestAsset = $this->goodRequestAssets->create([
                'assign_asset_id'=>$asset->id,
                'asset_condition'=>$inputs['asset_condition'],
                'status'=>2,
                'assigned_user_id'=>$inputs['assigned_user_id'],
                'assigned_district_id'=>NULL,
                'assigned_office_id'=>$inputs['assigned_office_id'],
                'assigned_department_id'=>$inventory['assigned_department_id'],
                'assigned_on'=>date('Y-m-d H:i:s'),
                'handover_status_id'=>0
            ]);

            DB::commit();
            return $asset;
        } catch (QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    private function generateAssetStatus($inputs)
    {
        $status = config('constant.ASSET_NEW');
        if ($inputs['assigned_user_id'] || $inputs['assigned_office_id']) {
            $status = config('constant.ASSET_ASSIGNED');
        } else {
            $status = config('constant.ASSET_ON_STORE');
        }
        if ($inputs['asset_disposed']) {
            $status = config('constant.ASSET_DISPOSED');
        }
        return $status;
    }

    private function generateAssetLogRemarks($inputs)
    {
        $remarks = 'Asset is created.';

        if ($inputs['assigned_user_id'] || $inputs['assigned_office_id']) {
            $remarks = 'Asset is assigned.';
        } else {
            $remarks = 'Asset in store.';
        }
        if ($inputs['asset_disposed']) {
            $remarks = 'Asset disposed.';
        }
        return $remarks;
    }
}
