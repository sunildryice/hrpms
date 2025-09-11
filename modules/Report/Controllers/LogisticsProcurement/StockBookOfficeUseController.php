<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Inventory\Models\InventoryItem;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\InventoryCategoryRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\LogisticsProcurement\StockBookOfficeUseExport;

class StockBookOfficeUseController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected ItemRepository $items,
        protected InventoryCategoryRepository $inventoryCategories,
        protected InventoryItemRepository $inventoryItems,
        protected FiscalYearRepository $fiscalYears,
        protected OfficeRepository $offices
    ) {}

    public function index(Request $request)
    {
        $data = InventoryItem::with([
            'unit',
            'item.category.inventoryType',
            'accountCode',
            'activityCode',
            'donorCode',
            'supplier',
            'goodRequestItems' => function ($q) {
                $q->whereHas('goodRequest', function ($q) {
                    $q->where('status_id', config('constant.ASSIGNED_STATUS'));
                    $q->orWhere(function ($q) {
                        $q->where('is_direct_dispatch', true)
                            ->where('status_id', config('constant.APPROVED_STATUS'));
                    });
                });
            },
            'goodRequestItems.goodRequest',
            'goodRequestItems.unit',
            'goodRequestItems.goodRequest.fiscalYear',
            'goodRequestItems.assignedInventoryItem.grn',
            'goodRequestItems.assignedInventoryItem.grn.fiscalYear',
            'goodRequestItems.goodRequest.requester',
            'goodRequestItems.goodRequest.office',
            'goodRequestItems.goodRequest.logs',
            'grn',
            'grn.fiscalYear',
            'grn.createdBy',
            'executionType',
            'distributionRequestItems' => function ($q) {
                $q->whereHas('distributionRequest', function ($q) {
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                });
            },
            'distributionRequestItems.distributionRequest',
            'distributionRequestItems.distributionRequest.fiscalYear',
            'distributionRequestItems.inventoryItem.grn.fiscalYear',
            'distributionType',
            'item',
            'item.category',
            'item.category.inventoryType',
        ]);
        $data->whereHas('distributionType', function ($q) {
            $q->where('title', 'office use');
        });

        // $data->whereHas('item', function ($q) {
        //     $q->whereHas('category', function ($q) {
        //         $q->whereHas('inventoryType', function ($q) {
        //             $q->where('title', '=', 'Consumable');
        //         });
        //     });
        // });

        $data->whereHas('category', function ($q) {
            $q->whereHas('inventoryType', function ($q) {
                $q->where('title', '=', 'Consumable');
            });
        });

        if ($request->filled('issued_to')) {
            $data->whereHas('goodRequestItems', function ($q) use ($request) {
                $q->whereHas('goodRequest', function ($q) use ($request) {
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                    $q->where('office_id', $request->issued_to);
                });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date <= $request->end_date) {
                $data->whereHas('goodRequestItems', function ($q) use ($request) {
                    $q->whereHas('goodRequest', function ($q) use ($request) {
                        $q->whereHas('logs', function ($q) use ($request) {
                            $q->where('status_id', config('constant.APPROVED_STATUS'));
                            $q->latest();
                            $q->whereDate('created_at', '>=', $request->start_date)
                                ->whereDate('created_at', '<=', $request->end_date);
                        });
                    });
                });
            }
        }

        if ($request->filled('item_id')) {
            $data = $data->where('item_id', $request->item_id);
        }

        if ($request->filled('item_category')) {
            $data = $data->where('category_id', $request->item_category);
        }
        $data = $data->get();

        $array = [
            'data' => $data,
            'employees' => $this->employees->getActiveEmployees(),
            'items' => $this->items->getActiveItems(),
            'inventoryCategories' => $this->inventoryCategories->get(),
            'years' => $this->fiscalYears->getFiscalYears(),
            'item_id' => $request->filled('item_id') ? $request->item_id : null,
            'items_in' => $request->filled('items_in') ? $request->items_in : null,
            'items_out' => $request->filled('items_out') ? $request->items_out : null,
            'issued_to' => $request->filled('issued_to') ? $request->issued_to : null,
            'location' => $request->filled('location') ? $request->location : null,
            'start_date' => $request->filled('start_date') ? $request->start_date : null,
            'end_date' => $request->filled('end_date') ? $request->end_date : null,
            'item_category' => $request->filled('item_category') ? $request->item_category : null,
            'staff_name' => $request->filled('staff_name') ? $request->staff_name : null,
            'offices' => $this->offices->getOffices(),
            'fiscal_year' => $request->filled('fiscal_year') ? $request->fiscal_year : null,
        ];
        // dd($data->last()->toArray());

        return view('Report::LogisticsProcurement.StockBook.StockBookOfficeUse.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->filled('start_date') ? $request->start_date : null;
        $end_date = $request->filled('end_date') ? $request->end_date : null;
        $item_id = $request->filled('item_id') ? $request->item_id : null;
        $item_category = $request->filled('item_category') ? $request->item_category : null;
        $issued_to = $request->filled('issued_to') ? $request->issued_to : null;

        return new StockBookOfficeUseExport($start_date, $end_date, $item_id, $item_category, $issued_to);
    }
}
