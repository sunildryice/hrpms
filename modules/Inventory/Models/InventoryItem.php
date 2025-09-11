<?php

namespace Modules\Inventory\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\DistributionRequest\Models\DistributionRequestItem;
use Modules\GoodRequest\Models\GoodRequestItem;
use Modules\Grn\Models\Grn;
use Modules\Grn\Models\GrnItem;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DistributionType;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Execution;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\InventoryCategory;
use Modules\Master\Models\Item;
use Modules\Master\Models\Office;
use Modules\Master\Models\Unit;
use Modules\Privilege\Models\User;
use Modules\Supplier\Models\Supplier;

class InventoryItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventory_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_id',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
        'grn_id',
        'grn_item_id',
        'category_id',
        'item_id',
        'unit_id',
        'supplier_id',
        'acquisition_method_id',
        'distribution_type_id',
        'expiry_date',
        'item_name',
        'model_name',
        'batch_number',
        'fiscal_year_id',
        'specification',
        'purchase_date',
        'quantity',
        'unit_price',
        'total_price',
        'vat_amount',
        'total_amount',
        'assigned_quantity',
        'execution_id',
        'voucher_number',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['purchase_date', 'expiry_date'];

    /**
     * Get the activityCode of the inventory item
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function getBatchNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->batch_number.$fiscalYear;
    }

    /**
     * Get the accountCode of the inventory item
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get all assets for the inventory item.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'inventory_item_id');
    }

    /**
     * Get the category of a inventory item
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id')->withDefault();
    }

    /**
     * Get the createdBy of a purchase
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the distributionType of the inventory item
     */
    public function distributionType()
    {
        return $this->belongsTo(DistributionType::class, 'distribution_type_id')->withDefault();
    }

    /**
     * Get the donorCode of the inventory item
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    public function executionType()
    {
        return $this->belongsTo(Execution::class, 'execution_id')->withDefault();
    }

    /**
     * Get the grn of the inventory item.
     */
    public function grn()
    {
        return $this->belongsTo(Grn::class, 'grn_id')->withDefault();
    }

    public function getDiscountAmount()
    {
        return $this->grn->discount_amount;
    }

    public function getTotalAmountAfterDiscount()
    {
        return $this->getTotalPrice() - $this->getDiscountAmount();
    }

    /**
     * Get the grn item of the inventory item.
     */
    public function grnItem()
    {
        return $this->belongsTo(GrnItem::class, 'grn_item_id')->withDefault();
    }

    /**
     * Get the item of the inventory item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    public function goodRequestItems()
    {
        return $this->hasMany(GoodRequestItem::class, 'assigned_inventory_item_id');
    }

    public function distributionRequestItems()
    {
        return $this->hasMany(DistributionRequestItem::class, 'inventory_item_id');
    }

    public function goodRequestAndDistributionItems()
    {
        if ($this->relationLoaded('goodRequestItems')) {
            $goodRequestItems = $this->goodRequestItems;
        } else {
            $goodRequestItems = $this->goodRequestItems()->whereHas('goodRequest', function ($q) {
                $q->where('status_id', config('constant.ASSIGNED_STATUS'));
                $q->orWhere(function ($q) {
                    $q->where('is_direct_dispatch', true)
                        ->where('status_id', config('constant.APPROVED_STATUS'));
                });
            })->get();
        }
        if ($this->relationLoaded('distributionRequestItems')) {
            $distributionRequestItems = $this->distributionRequestItems;
        } else {
            $distributionRequestItems = $this->distributionRequestItems()->whereHas('distributionRequest', function ($q) {
                $q->where('status_id', config('constant.APPROVED_STATUS'));
            })->get();
        }
        $items = $goodRequestItems->merge($distributionRequestItems);
        $collection = collect();

        if ($items->isNotEmpty()) {
            foreach ($items as $item) {
                if (class_basename(get_class($item)) == 'GoodRequestItem') {
                    $collection->push((object) collect([
                        'item' => $item->item_name,
                        'description' => $item->specification,
                        'unit' => $item->getUnit(),
                        'used_quantity' => $item->assigned_quantity,
                        'rate' => $item->assignedInventoryItem->getUnitPrice(),
                        'amount' => $item->assignedInventoryItem->getTotalPrice(),
                        'vat' => $item->assignedInventoryItem->getVatAmount(),
                        'total_amount' => $item->assignedInventoryItem->getTotalAmount(),
                        'issued_to' => $item->goodRequest->getRequesterName(),
                        'location' => $item->goodRequest->office->getOfficeName(),
                        'project' => $item->goodRequest->getProjectCode(),
                        'account_code' => $item->getAccountCode(),
                        'activity_code' => $item->getActivityCode(),
                        'donor_code' => $item->getDonorCode(),
                        'grn_number' => $item->assignedInventoryItem->grn->getGrnNumber(),
                        'stock_requisition_number' => $item->goodRequest->getGoodRequestNumber(),
                        'handover_date' => $item->goodRequest->handover_date,
                        'issued_date' => $item->goodRequest->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at->toFormattedDateString(),
                    ]));
                } else {
                    $collection->push((object) collect([
                        'item' => $item->getItemName(),
                        'description' => $item->specification,
                        'unit' => $item->getUnit(),
                        'used_quantity' => $item->quantity,
                        'assigned_quantity' => $item->assigned_quantity,
                        'rate' => $item->unit_price,
                        'amount' => $item->total_amount,
                        'vat' => $item->vat_amount,
                        'total_amount' => $item->net_amount,
                        'issued_to' => $item->distributionRequest->getRequesterName(),
                        'office_code' => $item->distributionRequest->office->getOfficeCode(),
                        // 'location'                 => $item->inventoryItem->distributionType->title == 'Distribution' ? $item->distributionRequest->health_facility_name : $item->distributionRequest->office->getOfficeName(),
                        'location' => $item->distributionRequest->district->getDistrictName(),
                        'health_facility' => $item->distributionRequest->getHealthFacility(),
                        'project' => $item->distributionRequest->getProjectCode(),
                        'account_code' => $item->getAccountCode(),
                        'activity_code' => $item->getActivityCode(),
                        'donor_code' => $item->getDonorCode(),
                        'grn_number' => $item->inventoryItem->grn->getGrnNumber(),
                        'stock_requisition_number' => $item->distributionRequest->getDistributionRequestNumber(),
                        'issued_date' => $item->distributionRequest->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at->toFormattedDateString(),
                    ]));
                }
            }
        }

        return $collection;
    }

    /**
     * Get the new assets (not assigned) for the inventory.
     */
    public function newAssets()
    {
        return $this->hasMany(Asset::class, 'inventory_item_id')
            ->where('status', config('constant.ASSET_NEW'));
    }

    /**
     * Get the office of the inventory item.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function getOfficeCode()
    {
        return $this->office->office_code;
    }

    /**
     * Get supplier of the inventory item.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    /**
     * Get unit of the inventory item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getAvailableQuantity()
    {
        return $this->quantity - $this->assigned_quantity;
    }

    public function getCategoryName()
    {
        return $this->item?->category?->title;
    }

    public function getInventoryCategoryName()
    {
        return $this->category->title;
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getConsumableFlag()
    {
        return $this->category->inventoryType->title == 'Consumable';
    }

    public function getConsumableType()
    {
        return $this->category->inventoryType->title;
    }

    public function getDistributionType()
    {
        return $this->distributionType->title;
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getExecutionType()
    {
        return $this->executionType->getTitle();
    }

    public function getExpiryDate()
    {
        return $this->expiry_date?->toFormattedDateString();
    }

    public function getGrnNumber()
    {
        return $this->grn?->getGrnNumber();
    }

    public function getItemName()
    {
        return ! is_null($this->item_name) ? $this->item_name : $this->item->title;
    }

    public function getPurchaseDate()
    {
        return $this->purchase_date ? $this->purchase_date->toFormattedDateString() : '';
    }

    public function getSupplierName()
    {
        return $this->supplier->supplier_name;
    }

    public function getUnitName()
    {
        return $this->unit->getUnitName();
    }

    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getVatAmount()
    {
        return $this->vat_amount;
    }

    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    public function getVoucherNumber()
    {
        return $this->voucher_number;
    }

    public function getHealthFacility()
    {
        return $this->distributionRequestItems()?->first()?->distributionRequest?->getHealthFacility();
    }

    public function getHealthFacilityDesc()
    {
        return $this->distributionRequestItems()?->first()?->distributionRequest?->getHealthFacilityDesc();
    }

    public function getAssetCodes()
    {
        return $this->assets->map(function ($asset) {
            return $asset->getAssetNumber();
        });
    }

    public function hasDuplicateAssets()
    {
        foreach ($this->assets as $asset) {
            if (Asset::where('inventory_item_id', '<>', $asset->inventory_item_id)
                ->where('asset_number', $asset->asset_number)
                ->where('prefix', $asset->prefix)
                ->where('year', $asset->year)
                ->count() == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Recalculate the assigned quantity of Consumable item inventory
     */
    public function updateAssignedQuantity()
    {
        if ($this->item->category->inventoryType->title != 'Consumable') {
            return;
        }

        if ($this->distribution_type_id == 2) {
            $assignedQuantity = $this->distributionRequestItems()->whereHas('distributionRequest', function ($q) {
                $q->where('status_id', config('constant.APPROVED_STATUS'));
            })->sum('quantity');
        } else {
            $assignedQuantity = $this->goodRequestItems()->withWhereHas('goodRequest', function ($q) {
                $q->where('status_id', config('constant.ASSIGNED_STATUS'));
                $q->orWhere(function ($q) {
                    $q->where('is_direct_dispatch', true)
                        ->where('status_id', config('constant.APPROVED_STATUS'));
                });
            })
                ->sum('assigned_quantity');
        }

        $this->assigned_quantity = $assignedQuantity;
        $this->save();
    }
}
