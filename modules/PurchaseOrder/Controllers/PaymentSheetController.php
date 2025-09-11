<?php

namespace Modules\PurchaseOrder\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetSubmitted;
use Modules\PaymentSheet\Repositories\PaymentBillRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\PaymentSheet\Requests\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseOrder\Requests\PaymentSheet\StoreRequest;
use Modules\Supplier\Repositories\SupplierRepository;

class PaymentSheetController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected PaymentBillRepository $paymentBills,
        protected PaymentSheetRepository $paymentSheets,
        protected ProjectCodeRepository $projectCodes,
        protected PurchaseOrderRepository $purchaseOrders,
        protected SupplierRepository $suppliers,
        protected UserRepository $users
    ) {
        $this->destinationPath = 'purhaseOrder';
    }

    public function index(Request $request, $id)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $purchaseOrder = $this->purchaseOrders->find($id);
            $data = $purchaseOrder->paymentSheets()->with(['fiscalYear', 'status', 'supplier'])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row) {
                    return $row->getSupplierVatPanNumber();
                })->addColumn('payment_sheet_number', function ($row) {
                    return $row->getPaymentSheetNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('payment.sheets.show', $row->id).'" rel="tooltip" title="View Payment Sheet"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('payment.sheets.edit', $row->id).'" rel="tooltip" title="Edit Payment Sheet"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('payment.sheets.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if (! in_array($row->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS'),
                        config('constant.SUBMITTED_STATUS'), config('constant.CANCELLED_STATUS'), config('constant.REJECTED_STATUS')])) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('payment.sheets.print', $row->id).'" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                        $btn .= '<i class="bi bi-printer"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::index');
    }

    public function create($id)
    {
        $suppliers = $this->suppliers->getActiveSuppliers();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $districts = $this->districts->getEnabledDistricts();
        $purchaseOrder = $this->purchaseOrders->find($id);
        $purchaseOrders = $this->purchaseOrders->where('supplier_id', '=', $purchaseOrder->supplier_id)
            ->where('status_id', '=', config('constant.APPROVED_STATUS'))
            ->orderBy('order_number', 'desc')->get();

        $paymentBills = $this->paymentBills->select(['*'])
            ->where(function ($q) use ($purchaseOrder) {
                $q->where('supplier_id', '=', $purchaseOrder->supplier_id)
                    ->where('office_id', '=', auth()->user()->employee->office_id)
                    ->whereRaw('settled_amount < bill_amount');
            })->get();

        return view('PurchaseOrder::PaymentSheet.create', compact('paymentBills'))
            ->with([
                'tdsPercentages' => Helper::tdsPercentages(),
                'districts' => ($districts),
                'purchaseOrder' => ($purchaseOrder),
                'purchaseOrders' => ($purchaseOrders),
                'suppliers' => ($suppliers),
                'projectCodes' => ($projectCodes),
            ]);
    }

    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($id);
        $inputs = $request->validated();
        $inputs['supplier_id'] = $purchaseOrder->supplier_id;
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentSheet = $this->paymentSheets->createFromPo($inputs);

        if ($paymentSheet) {
            return response()->json(['success' => true, 'message' => 'Payment sheet successfully added.', 'payment_sheet_id' => $paymentSheet->id], 200);
        }

        return response()->json(['success' => false, 'message' => 'Payment sheet can not be added.'], 422);
    }

    public function show($paymentSheetId)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);

        return view('PaymentSheet::show')
            ->withPaymentSheet($paymentSheet);
    }

    public function edit($id)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($id);
        $this->authorize('update', $paymentSheet);

        $verifiers = $this->users->permissionBasedUsers('verify-payment-sheet');
        $approvers = $this->users->getSupervisors($paymentSheet->requester);
        $suppliers = $this->suppliers->getActiveSuppliers();
        $purchaseOrders = $this->purchaseOrders->select(['id', 'prefix', 'order_number', 'supplier_id'])
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where('supplier_id', $paymentSheet->supplier_id)
            ->orderBy('order_number', 'desc')->get();
        $districts = $this->districts->getEnabledDistricts();

        return view('PaymentSheet::edit')
            ->withAuthUser($authUser)
            ->withApprovers($approvers)
            ->withDistricts($districts)
            ->withPaymentSheet($paymentSheet)
            ->withProjectCodes($this->projectCodes->getActiveProjectCodes())
            ->withPurchaseOrders($purchaseOrders)
            ->withVerifiers($verifiers)
            ->withSuppliers($suppliers);
    }

    public function update(UpdateRequest $request, $id)
    {
        $paymentSheet = $this->paymentSheets->find($id);
        $this->authorize('update', $paymentSheet);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        if ($inputs['btn'] == 'submit') {
            $billIds = $paymentSheet->paymentSheetDetails->pluck('payment_bill_id')->unique()->toArray();
            $billAmount = $this->paymentBills->select(['id', 'bill_amount'])
                ->whereIn('id', $billIds)->sum('bill_amount');
            $paymentAmount = (float) $paymentSheet->paymentSheetDetails->sum('total_amount');

            $billAmount = round($billAmount, 2);
            $paymentAmount = round($paymentAmount, 2);

            if ($paymentAmount != $billAmount) {
                return redirect()->back()->withInput()
                    ->withWarningMessage('Payment sheet can not be submitted. All bills are not charged with 100% within payment sheet.');
            }
        }
        $paymentSheet = $this->paymentSheets->update($id, $inputs);

        if ($paymentSheet) {
            $message = 'Payment sheet is successfully updated.';
            if ($paymentSheet->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Payment sheet is successfully submitted.';
                $paymentSheet->verifier->notify(new PaymentSheetSubmitted($paymentSheet));

                return redirect()->route('payment.sheets.index')
                    ->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Payment sheet can not be updated.');
    }
}
