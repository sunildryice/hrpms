<?php

namespace Modules\Grn\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Grn\Requests\Item\Add\UpdateRequest as ItemUpdateRequest;
use Modules\Grn\Requests\StoreRequest;
use Modules\Grn\Requests\UpdateRequest;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Supplier\Repositories\SupplierRepository;
use ReflectionClass;
use Yajra\DataTables\DataTables;

class GrnController extends Controller
{
    private $districts;

    private $employees;

    private $fiscalYears;

    private $grns;

    private $suppliers;

    private $users;

    private $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        DistrictRepository $districts,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        GrnRepository $grns,
        SupplierRepository $suppliers,
        UserRepository $users,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseOrderRepository $purchaseOrders
    ) {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->grns = $grns;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->purchaseRequests = $purchaseRequests;
        $this->purchaseOrders = $purchaseOrders;
        $this->destinationPath = 'grn';
    }

    /**
     * Display a listing of the grns
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('grn');
        if ($request->ajax()) {
            $data = $this->grns->with(['fiscalYear', 'status', 'grnItems', 'grnable'])
                ->whereCreatedBy($authUser->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('fiscal_year_id', 'desc')
                ->orderBy('grn_number', 'desc')
                ->get();

            // $data = $data->sortBy(function ($item, $index) {
            //     if ($item->status_id == config('constant.CREATED_STATUS')) {
            //         return -1;
            //     }
            //     return $index;
            // });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('received_date', function ($row) {
                    return $row->getReceivedDate();
                })->addColumn('order_number', function ($row) {
                    return $row->getGrnableNumber();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('grn_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('grns.show', $row->id).'" rel="tooltip" title="View GRN"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('grns.edit', $row->id).'" rel="tooltip" title="Edit GRN"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('grns.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($row->status_id == config('constant.APPROVED_STATUS')) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('grns.print', $row->id).'" rel="tooltip" title="Print GRN"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('unreceive', $row)) {
                        // $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        // $btn .= route('grns.unreceive', $row->id) . '" rel="tooltip" title="Unreceive GRN"><i class="bi-bootstrap-reboot"></i></a>';

                        $btn .= '&emsp;<a href = "javascript:;" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Unreceive GRN" class="btn btn-primary btn-sm unreceive-grn"';
                        $btn .= 'data-href = "'.route('grns.unreceive.store', $row->id).'" >';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'grn_number', 'supplier', 'status'])
                ->make(true);
        }

        return view('Grn::index');
    }

    /**
     * Show the form to create new grn.
     *
     * @param  $grnId
     * @return mixed
     */
    public function create()
    {
        $authUser = auth()->user();
        $suppliers = $this->suppliers->getActiveSuppliers();

        return view('Grn::create')
            ->withSuppliers($suppliers);
    }

    /**
     * Store a newly created grn in storage.
     *
     * @return mixed
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['status_id'] = config('constant.CREATED_STATUS');
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $grn = $this->grns->create($inputs);

        if ($grn) {
            return redirect()->route('grns.edit', $grn->id)
                ->withSuccessMessage('Good receive note successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Good receive note can not be added.');
    }

    /**
     * Show the specified grn.
     *
     * @return mixed
     */
    public function show($grnId)
    {
        $authUser = auth()->user();
        $grn = $this->grns->with(['grnItems.item', 'grnItems.unit'])->find($grnId);

        return view('Grn::show')
            ->withGrn($grn);
    }

    /**
     * Show the form for editing the specified grn.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($id);
        $this->authorize('update', $grn);
        $suppliers = $this->suppliers->getActiveSuppliers();

        $view = view('Grn::edit');
        $grnableType = $grn->grnable_type;
        if ($grnableType) {
            $reflection = new ReflectionClass($grnableType);
            $modelName = $reflection->getShortName();
            if ($modelName == 'PurchaseRequest') {
                $view = view('Grn::editRequest');
            } else {
                $view = view('Grn::editOrder');
            }
        }

        return $view->withAuthUser($authUser)
            ->withGrn($grn)
            ->withSuppliers($suppliers);
    }

    /**
     * Update the specified grn in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $grn = $this->grns->find($id);
        $this->authorize('update', $grn);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $grn = $this->grns->update($id, $inputs);
        if ($grn) {
            $message = 'GRN is successfully updated.';
            if ($grn->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'GRN is successfully received.';

                return redirect()->route('grns.index')
                    ->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('GRN can not be updated.');
    }

    /**
     * Remove the specified purchase request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $grn = $this->grns->find($id);
        $this->authorize('delete', $grn);
        $flag = $this->grns->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'GRN is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'GRN can not deleted.',
        ], 422);
    }

    /**
     * Show the specified grn in printable view
     *
     * @return mixed
     */
    public function printGrn($id)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($id);

//        if ($grn->grnable_type == config('constant.PURCHASE_ORDER')) {
//            $items = $grn
//                ->grnItems()
//                ->with([
//                    'grnitemable' => function ($q) {
//                        $q->select('id', 'purchase_request_item_id')->with([
//                            'purchaseRequestItem' => function ($q) {
//                                $q->select(['id', 'office_id']);
//                            },
//                        ]);
//                    },
//                ])
//                ->get()
//                ->map(function ($item) {
//                    $item->office_id = $item->grnitemable->purchaseRequestItem->office_id;
//
//                    return $item;
//                });
//        } elseif ($grn->grnable_type == config('constant.PURCHASE_REQUEST')) {
//            $items = $grn
//                ->grnItems()
//                ->with([
//                    'grnitemable' => function ($q) {
//                        $q->select('id', 'office_id');
//                    },
//                ])
//                ->get()
//                ->map(function ($item) {
//                    $item->office_id = $item->grnitemable->office_id;
//
//                    return $item;
//                });
//        } else {
//            $items = $grn->grnItems;
//        }
//
//        $summaries = $items
//            ->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])
//            ->flatten(2)
//            ->map(function ($grnItem) {
//                $summary = (object)[];
//                foreach ($grnItem as $index => $item) {
//                    if ($index == 0) {
//                        $summary = $item;
//                        continue;
//                    }
//                    $summary->total_price += $item->total_price;
//                    $summary->vat_amount += $item->vat_amount;
//                    $summary->discount_amount += $item->discount_amount;
//                    $summary->tds_amount += $item->tds_amount;
//                    $summary->total_amount += $item->total_amount;
//                }
//                return $summary;
//            });

        return view('Grn::print', ['grn' => $grn]);
    }

    public function unreceive($id)
    {
        $grn = $this->grns->find($id);

        $this->authorize('unreceive', $grn);

        DB::beginTransaction();
        try {
            $grn->update([
                'status_id' => config('constant.CREATED_STATUS'),
                'reviewer_id' => null,
                'approver_id' => null,
            ]);

            $grn->logs()->create([
                'user_id' => auth()->user()->id,
                'status_id' => config('constant.CREATED_STATUS'),
                'log_remarks' => 'GRN unreceived.',
                'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
            ]);
            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'GRN successfully unreceived.',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);

            return response()->json([
                'type' => 'error',
                'message' => 'GRN can not be unreceived.',
            ], 422);
        }
    }

    public function addItem($grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $grnableType = $grn->grnable_type;
        if ($grnableType) {
            $reflection = new ReflectionClass($grnableType);
            $modelName = $reflection->getShortName();
            if ($modelName == 'PurchaseRequest') {
                $purchaseRequest = $this->purchaseRequests->find($grn->grnable_id);

                return view('Grn::Item.Add.create')
                    ->withItems($purchaseRequest->purchaseRequestItems)
                    ->withModelName($modelName)
                    ->withGrn($grn);
            }
            $purchaseOrder = $this->purchaseOrders->find($grn->grnable_id);

            return view('Grn::Item.Add.create')
                ->withItems($purchaseOrder->purchaseOrderItems)
                ->withModelName($modelName)
                ->withGrn($grn);
        }

        return redirect()->back()->withInput()->withWarningMessage('Items can not be added.');
    }

    public function updateItem(ItemUpdateRequest $request, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $inputs = $request->validated();
        $grnableType = $grn->grnable_type;
        if ($grnableType) {
            $reflection = new ReflectionClass($grnableType);
            $modelName = $reflection->getShortName();
            if ($modelName == 'PurchaseRequest') {
                $inputs['purchase_request_id'] = $grn->grnable_id;
                $grn = $this->grns->updateFromPr($grnId, $inputs);
            } else {
                $grn = $this->grns->updateFromPo($grnId, $inputs);
            }
        }
        if ($grn) {
            $message = 'GRN is successfully updated.';

            return redirect()->route('grns.edit', $grnId)
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('GRN can not be updated. Received quantity can not be zero and can not exceed total order quantity for a item.');
    }
}
