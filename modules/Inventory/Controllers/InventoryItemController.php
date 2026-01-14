<?php

namespace Modules\Inventory\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Inventory\Models\InventoryItem;
use Modules\Inventory\Requests\StoreRequest;
use Modules\Inventory\Requests\UpdateRequest;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ExecutionRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Supplier\Repositories\SupplierRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\InventoryTypeRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\DistributionTypeRepository;

class InventoryItemController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected DistributionTypeRepository $distributionTypes,
        protected DonorCodeRepository $donorCodes,
        protected ExecutionRepository $executions,
        protected GrnRepository $grns,
        protected InventoryItemRepository $inventoryItems,
        protected ItemRepository $items,
        protected SupplierRepository $suppliers,
        protected FiscalYearRepository $fiscalYear,
        protected InventoryTypeRepository $inventoryTypes,
        protected OfficeRepository $offices,
    ) {
        $this->destinationPath = 'inventory';
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');
        if ($request->ajax()) {
            $data = $this->inventoryItems->select(['*'])->orderBy('purchase_date', 'desc');
            if ($grnId = $request->query('grn_id')) {
                $data = $data->where('grn_id', $grnId);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('unit_price', function ($row) {
                    return $row->getUnitPrice();
                })
                ->addColumn('total_price', function ($row) {
                    return $row->getTotalPrice();
                })
                ->addColumn('vat_amount', function ($row) {
                    return $row->getVatAmount();
                })
                ->addColumn('total_amount', function ($row) {
                    return $row->getTotalAmount();
                })
                ->addColumn('available_quantity', function ($row) {
                    return $row->quantity - $row->assigned_quantity;
                })->addColumn('purchase_date', function ($row) {
                    return $row->getPurchaseDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('activity_code', function ($row) {
                    return $row->activityCode->getActivityCode();
                })->addColumn('account_code', function ($row) {
                    return $row->accountCode->getAccountCode();
                })->addColumn('donor_code', function ($row) {
                    return $row->donorCode->getDonorCode();
                })->addColumn('specification', function ($row) {
                    return $row->specification;
                })->addColumn('execution_type', function ($row) {
                    return $row->getExecutionType();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('inventories.show', $row->id).'" rel="tooltip" title="View Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('inventories.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Inventory::index', ['grn_id' => $request->query('grn_id')]);
    }

    /**
     * Index of consumable inventory items
     *
     * @return mixed
     */
    public function officeUseIndex(Request $request)
    {
        $authUser = auth()->user();
        $inventoryTypes = $this->inventoryTypes->select(['id', 'title'])->get();
        $this->authorize('manage-inventory');
        $data = InventoryItem::query();
        if ($request->ajax()) {
            $data = $data->with([
                'executionType',
                'supplier',
                'activityCode',
                'accountCode',
                'donorCode',
            ])
                ->select(['*'])
                ->whereHas('distributionType', function ($q) {
                    $q->where('title', 'office use');
                });

            $data->whereHas('item', function ($q) use ($request) {
                $q->whereHas('category', function ($q) use ($request) {
                    $q->where('inventory_type_id', ($request->item_type ?? 3));
                });
            });

            $data = $data->orderBy('purchase_date', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('unit_price', function ($row) {
                    return $row->getUnitPrice();
                })
                ->addColumn('total_price', function ($row) {
                    return $row->getTotalPrice();
                })
                ->addColumn('vat_amount', function ($row) {
                    return $row->getVatAmount();
                })
                ->addColumn('total_amount', function ($row) {
                    return $row->getTotalAmount();
                })
                ->addColumn('available_quantity', function ($row) {
                    return $row->quantity - $row->assigned_quantity;
                })->addColumn('purchase_date', function ($row) {
                    return $row->getPurchaseDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('activity_code', function ($row) {
                    return $row->activityCode->getActivityCode();
                })->addColumn('account_code', function ($row) {
                    return $row->accountCode->getAccountCode();
                })->addColumn('donor_code', function ($row) {
                    return $row->donorCode->getDonorCode();
                })->addColumn('specification', function ($row) {
                    return $row->specification;
                })->addColumn('execution_type', function ($row) {
                    return $row->getExecutionType();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('inventories.show', [$row->id, 'inv_type' => 'consumable']).'" rel="tooltip" title="View Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('inventories.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Inventory::consumable.index')->with('inv_type', 'Consumable')->with('inventoryTypes', $inventoryTypes);
    }

    /**
     * Index of Inventory Items with category type 'Distribution'
     *
     * @return mixed
     */
    public function distributionIndex(Request $request)
    {
        $authUser = auth()->user();
        $inventoryTypes = $this->inventoryTypes->select(['id', 'title'])->get();
        $this->authorize('manage-inventory');
        $data = InventoryItem::query();
        if ($request->ajax()) {
            $data = $data->with([
                'executionType',
                'supplier',
                'activityCode',
                'accountCode',
                'donorCode',
            ])
                ->select(['*'])
                ->where('distribution_type_id', 2);

            if ($request->has('item_type') && $request->item_type) {
                $data->whereHas('item', function ($q) use ($request) {
                    $q->whereHas('category', function ($q) use ($request) {
                        $q->where('inventory_type_id', $request->item_type);
                    });
                });
            }

            $data = $data->orderBy('purchase_date', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('unit_price', function ($row) {
                    return $row->getUnitPrice();
                })
                ->addColumn('total_price', function ($row) {
                    return $row->getTotalPrice();
                })
                ->addColumn('vat_amount', function ($row) {
                    return $row->getVatAmount();
                })
                ->addColumn('total_amount', function ($row) {
                    return $row->getTotalAmount();
                })
                ->addColumn('available_quantity', function ($row) {
                    return $row->quantity - $row->assigned_quantity;
                })->addColumn('purchase_date', function ($row) {
                    return $row->getPurchaseDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('activity_code', function ($row) {
                    return $row->activityCode->getActivityCode();
                })->addColumn('account_code', function ($row) {
                    return $row->accountCode->getAccountCode();
                })->addColumn('donor_code', function ($row) {
                    return $row->donorCode->getDonorCode();
                })->addColumn('specification', function ($row) {
                    return $row->specification;
                })->addColumn('execution_type', function ($row) {
                    return $row->getExecutionType();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('inventories.show', [$row->id, 'inv_type' => 'distribution']).'" rel="tooltip" title="View Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('inventories.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Inventory::distribution.index')->withInventoryTypes($inventoryTypes);
    }

    /**
     * Show the form for creating a new purchase request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $items = $this->items->getActiveItems();
        $suppliers = $this->suppliers->getActiveSuppliers();
        $executionTypes = $this->executions->getExecutions();

        return view('Inventory::create')
            ->withActivityCodes($activityCodes)
            ->withDistributionTypes($this->distributionTypes->get())
            ->withDonorCodes($donorCodes)
            ->withExecutionTypes($executionTypes)
            ->withItems($items)
            ->withOffices($this->offices->select(['*'])->whereNotNull('activated_at')->get())
            ->withSuppliers($suppliers);
    }

    /**
     * Store a newly created purchase request in storage.
     *
     * @param  \Modules\PurchaseRequest\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');
        $inputs = $request->validated();
        $item = $this->items->find($request->item_id);
        $distributionType = $this->distributionTypes->find($request->distribution_type_id);
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['created_by'] = auth()->id();
        $inputs['category_id'] = $item->inventory_category_id;
        $inputs['item_name'] = $item->title;
        // $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['asset_flag'] = $item->category->getInventoryType() != 'Consumable' && $distributionType->title != 'Distribution';
        $inputs['fiscal_year_id'] = $this->fiscalYear->getCurrentFiscalYearId();
        $inventoryItem = $this->inventoryItems->create($inputs);

        if ($inventoryItem) {
            return redirect()->route('inventories.index')
                ->withSuccessMessage('Inventory successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Inventory can not be added.');
    }

    public function edit($inventoryId)
    {
        $inventory = $this->inventoryItems->find($inventoryId);
        $this->authorize('update', $inventory);
        $canEditPrice = Gate::allows('updatePrice', $inventory);

        return view('Inventory::Inventory.edit')
            ->with([
                'inventory' => ($inventory),
                'canEditPrice' => $canEditPrice,
            ]);
    }

    public function update(UpdateRequest $request, InventoryItem $inventory)
    {
        $inputs = $request->validated();
        $inventoryId = $inventory->id;
        $this->authorize('update', $inventory);

        if (isset($inputs['unit_price'])) {
            $inputs['total_price'] = $inputs['unit_price'] * $inventory->quantity;

            $inputs['vat_amount'] = 0;
            if ($request->vat_applicable) {
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $inputs['vat_amount'] = $inputs['total_price'] * $vatPercentage / 100;
            }

            $inputs['total_amount'] = $inputs['total_price'] + $inputs['vat_amount'];
        }

        $inventory = $this->inventoryItems->update($inventoryId, $inputs);
        if ($inventory) {
            return response()->json([
                'type' => 'success',
                'message' => 'Inventory is successfully updated.',
                'spec' => $inventory->specification,
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Inventory can not be updated.',
        ], 422);
    }

    /**
     * Show the specified inventory item.
     *
     * @return mixed
     */
    public function show($inventoryId)
    {
        $authUser = auth()->user();
        $inventory = $this->inventoryItems->find($inventoryId);
        $invType = request()->query('inv_type');
        if ($invType == 'consumable') {
            $view = view('Inventory::consumable.show');
        } elseif ($invType == 'distribution') {
            $view = view('Inventory::distribution.show');
        } else {
            $view = view('Inventory::show');
        }

        return $view
            ->withInventory($inventory);
    }

    /**
     * Remove the specified purchase request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {
        $inventoryItem = $this->inventoryItems->find($id);
        $this->authorize('delete', $inventoryItem);
        $flag = $this->inventoryItems->destroy($id);
        if ($flag) {
            if ($request->ajax()) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'Inventory is successfully deleted.',
                ], 200);
            }

            return redirect()->route('inventories.index')
                ->withSuccessMessage('Inventory successfully deleted.');
        }
        if ($request->ajax()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Inventory cannot be deleted.',
            ], 422);
        }

        return redirect()->back()
            ->withErrorMessage('Inventory cannot be deleted.');
    }
}
