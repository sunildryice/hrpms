<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Inventory\Models\Asset;
use Modules\Inventory\Models\Enums\ItemRatePriceRange;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\DispositionTypeRepository;
use Modules\Master\Repositories\InventoryCategoryRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\LogisticsProcurement\AssetBookExport;
use Modules\Report\Exports\LogisticsProcurement\AssetDispositionExport;

class AssetBookController extends Controller
{
    protected $offices;

    public function __construct(
        protected AssetRepository $assets,
        protected ItemRepository $items,
        protected InventoryCategoryRepository $inventoryCategories,
        protected EmployeeRepository $employees,
        protected DispositionTypeRepository $dispositionTypes,
        OfficeRepository $offices,
    ) {
        $this->offices = $offices;
    }

    public function index(Request $request)
    {
        $data = Asset::query()->with([
            'inventoryItem.office',
            'inventoryItem.grn.fiscalYear',
            'inventoryItem.item',
            'inventoryItem.accountCode',
            'inventoryItem.activityCode',
            'inventoryItem.donorCode',
            'inventoryItem.executionType',
            'inventoryItem.supplier',
            'inventoryItem.category',
            'assignedTo',
            'assignedTo.employee.latestTenure.designation',
            'assignedTo.employee.latestTenure.office',
            'latestGoodRequestAsset.goodRequest.approvedLog',
            'latestConditionLog.condition',
            'assignedOffice',
        ])->whereDoesntHave('dispositionRequest', function ($query) {
            $query->where('status_id', config('constant.APPROVED_STATUS'));
        });

        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date <= $request->end_date) {
                $data->whereIn('status', [config('constant.ASSET_ASSIGNED')])
                    ->whereHas('goodRequestAsset', function ($q) use ($request) {
                        $q->whereHas('goodRequest', function ($q) use ($request) {
                            $q->whereHas('logs', function ($q) use ($request) {
                                $q->where('status_id', config('constant.APPROVED_STATUS'))
                                    ->whereDate('created_at', '>=', $request->start_date)
                                    ->whereDate('created_at', '<=', $request->end_date);
                            });
                        });
                    });
            }
        }

        if ($request->filled('issued_to')) {
            $data->where('status', config('constant.ASSET_ASSIGNED'))
                ->where('assigned_user_id', $request->issued_to);
        }

        if ($request->filled('issued_to_office')) {
            $data->where('assigned_office_id', $request->issued_to_office);
        }

        if ($request->filled('item_category')) {
            $data->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('category_id', $request->item_category);
            });
        }

        if ($request->filled('item_id')) {
            $data->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        if ($request->filled('price_range')) {
            $priceFilter = ItemRatePriceRange::tryFrom($request->price_range);
            if ($priceFilter) {
                $data = $priceFilter->apply($data);
            }
        }

        $data = $data->get();

        $priceRanges = ItemRatePriceRange::cases();

        $array = [
            'data' => $data,
            'employees' => $this->employees->getActiveEmployees(),
            'offices' => $this->offices->getActiveOffices(),
            'items' => $this->items->getActiveItems(),
            'offices' => $this->offices->getActiveOffices(),
            'priceRanges' => $priceRanges,
            'inventoryCategories' => $this->inventoryCategories->get(),
            'start_date' => $request->filled('start_date') ? $request->start_date : null,
            'end_date' => $request->filled('end_date') ? $request->end_date : null,
            'item_id' => $request->filled('item_id') ? $request->item_id : null,
            'item_category' => $request->filled('item_category') ? $request->item_category : null,
            'price_range' => $request->filled('price_range') ? $request->price_range : null,
            'issued_to' => $request->filled('issued_to') ? $request->issued_to : null,
            'issued_to_office' => $request->filled('issued_to_office') ? $request->issued_to_office : null,
        ];

        return view('Report::LogisticsProcurement.AssetBook.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->filled('start_date') ? $request->start_date : null;
        $end_date = $request->filled('end_date') ? $request->end_date : null;
        $item_id = $request->filled('item_id') ? $request->item_id : null;
        $item_category = $request->filled('item_category') ? $request->item_category : null;
        $issued_to = $request->filled('issued_to') ? $request->issued_to : null;
        $issued_to_office = $request->filled('issued_to_office') ? $request->issued_to_office : null;
        $priceRange = ItemRatePriceRange::tryFrom($request->price_range);

        return new AssetBookExport($start_date, $end_date, $item_id, $item_category, $issued_to, $issued_to_office, $priceRange);
    }

    public function dispositionIndex(Request $request)
    {
        $data = Asset::query()->with('dispositionRequest')
            ->whereHas('dispositionRequest', function ($q) {
                $q->where('status_id', config('constant.APPROVED_STATUS'));
            });

        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date <= $request->end_date) {
                $data
                    ->whereHas('dispositionRequest', function ($q) use ($request) {
                        $q->where('disposition_date', '>=', $request->start_date);
                        $q->where('disposition_date', '<=', $request->end_date);
                    });
            }
        }

        if ($request->filled('requester')) {
            $data->whereHas('dispositionRequest', function ($q) use ($request) {
                $q->where('requester_id', $request->requester);
            });
        }

        if ($request->filled('item_category')) {
            $data->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('category_id', $request->item_category);
            });
        }

        if ($request->filled('disposition_type')) {
            $data->whereHas('dispositionRequest', function ($q) use ($request) {
                $q->where('disposition_type_id', $request->disposition_type);
            });
        }

        if ($request->filled('office_id')) {
            $data->whereHas('dispositionRequest', function ($q) use ($request) {
                $q->where('office_id', $request->office_id);
            });
        }

        if ($request->filled('item_id')) {
            $data->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $data = $data->get();

        $array = [
            'data' => $data,
            'employees' => $this->employees->getActiveEmployees(),
            'items' => $this->items->getActiveItems(),
            'inventoryCategories' => $this->inventoryCategories->get(),
            'dispositionTypes' => $this->dispositionTypes->getDispositionTypes(),
            'offices' => $this->offices->getActiveOffices(),
            'start_date' => $request->filled('start_date') ? $request->start_date : null,
            'end_date' => $request->filled('end_date') ? $request->end_date : null,
            'item_id' => $request->filled('item_id') ? $request->item_id : null,
            'item_category' => $request->filled('item_category') ? $request->item_category : null,
            'requester' => $request->filled('requester') ? $request->requester : null,
            'disposition_type' => $request->filled('disposition_type') ? $request->disposition_type : null,
            'office_id' => $request->filled('office_id') ? $request->office_id : null,
        ];

        return view('Report::LogisticsProcurement.AssetDisposition.index', $array);
    }

    public function dispositionExport(Request $request)
    {
        $start_date = $request->filled('start_date') ? $request->start_date : null;
        $end_date = $request->filled('end_date') ? $request->end_date : null;
        $item_id = $request->filled('item_id') ? $request->item_id : null;
        $item_category = $request->filled('item_category') ? $request->item_category : null;
        $requester = $request->filled('requester') ? $request->requester : null;
        $disposition_type = $request->filled('disposition_type') ? $request->disposition_type : null;
        $office_id = $request->filled('office_id') ? $request->office_id : null;

        return new AssetDispositionExport($start_date, $end_date, $item_id, $item_category, $requester, $disposition_type, $office_id, $priceRange);
    }
}
