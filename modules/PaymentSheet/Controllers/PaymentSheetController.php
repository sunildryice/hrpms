<?php

namespace Modules\PaymentSheet\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetSubmitted;
use Modules\PaymentSheet\Repositories\PaymentBillRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\PaymentSheet\Requests\StoreRequest;
use Modules\PaymentSheet\Requests\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Supplier\Repositories\SupplierRepository;

class PaymentSheetController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PaymentSheetRepository $paymentSheets
     * @param ProjectCodeRepository $projectCodes
     * @param SupplierRepository $suppliers
     * @param UserRepository $users
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
        $this->destinationPath = 'paymentSheet';
    }

    /**
     * Display a listing of the payment sheets
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->paymentSheets->with(['fiscalYear', 'status', 'supplier'])
                ->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('created_by', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
                $q->orWhere('original_user_id', $authUser->id);
            })
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row) {
                return $row->getSupplierVatPanNumber();
            })->addColumn('payment_sheet_number', function ($row) {
                return $row->getPaymentSheetNumber();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('payment.sheets.show', $row->id) . '" rel="tooltip" title="View Payment Sheet"><i class="bi bi-eye"></i></a>';
                if ($authUser->can('update', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('payment.sheets.edit', $row->id) . '" rel="tooltip" title="Edit Payment Sheet"><i class="bi-pencil-square"></i></a>';
                }
                if (!in_array($row->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS'),
                    config('constant.SUBMITTED_STATUS'), config('constant.CANCELLED_STATUS'), config('constant.REJECTED_STATUS')])) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('payment.sheets.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                }
                if ($authUser->can('delete', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('payment.sheets.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                } else if ($authUser->can('amend', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-danger amend-record"';
                    $btn .= 'data-href = "' . route('payment.sheets.amend', $row->id) . '" data-number="' . $row->getPaymentSheetNumber() . '" title="Reverse Payment Sheet">';
                    $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                }

                return $btn;
            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::index');
    }

    /**
     * Show the form for creating a new payment sheet by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $suppliers = $this->suppliers->getActiveSuppliers();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $districts = $this->districts->getEnabledDistricts();
        $approvers = $this->users->permissionBasedUsers('approve-payment-sheets');
        return view('PaymentSheet::create', ['approvers' => $approvers])
            ->withDistricts($districts)
            ->withSuppliers($suppliers)
            ->withProjectCodes($projectCodes);
    }

    /**
     * Store a newly created payment sheet in storage.
     *
     * @param \Modules\PaymentSheet\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentSheet = $this->paymentSheets->create($inputs);

        if ($paymentSheet) {
            return redirect()->route('payment.sheets.edit', $paymentSheet->id)
                ->withSuccessMessage('Payment sheet successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Payment sheet can not be added.');
    }

    /**
     * Show the specified payment sheet.
     *
     * @param $paymentSheetId
     * @return mixed
     */
    public function show($paymentSheetId)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);

        return view('PaymentSheet::show')
            ->withPaymentSheet($paymentSheet);
    }

    /**
     * Show the form for editing the specified payment sheet.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($id);
        $this->authorize('update', $paymentSheet);
        // dd($paymentSheet->deduction_amount, $paymentSheet->toArray());

        $verifiers = $this->users->permissionBasedUsers('verify-payment-sheet');
        // $approvers = $this->users->getSupervisors($paymentSheet->requester);
        $approvers = $this->users->permissionBasedUsers('approve-payment-sheet');
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

    /**
     * Update the specified payment sheet in storage.
     *
     * @param \Modules\PaymentSheet\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
            $paymentAmount = (double) $paymentSheet->paymentSheetDetails->sum('total_amount');

            $billAmount = round($billAmount, 2);
            $paymentAmount = round($paymentAmount, 2);

            if ($paymentAmount != $billAmount) {
                return redirect()->back()->withInput()
                    ->withWarningMessage('Payment sheet can not be submitted. All bills are not charged with 100% within payment sheet. Bill Amount: '. $billAmount . ' Payemnt Amount: ' . $paymentAmount);
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

    /**
     * Remove the specified payment sheet from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $paymentSheet = $this->paymentSheets->find($id);
        $this->authorize('delete', $paymentSheet);
        $flag = $this->paymentSheets->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Payment sheet is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Payment sheet can not deleted.',
        ], 422);
    }

    /**
     * Show the specified payment sheet in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printBill($id)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($id);

        return view('PaymentSheet::print')
            ->withPaymentSheet($paymentSheet);
    }

    public function print($paymentSheetId)
    {
        $paymentRequest = $this->paymentSheets->find($paymentSheetId);

        return view('PaymentSheet::Approved.print')
            ->withPaymentSheet($paymentRequest);
    }

    public function amend(Request $request, $id)
    {
        $paymentSheet = $this->paymentSheets->find($id);
        $this->authorize('amend', $this->paymentSheets->find($id));
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);
        $inputs['status_id'] = config('constant.RETURNED_STATUS');
        $inputs['user_id'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $flag = $this->paymentSheets->amend($paymentSheet->id, $inputs);

        if ($flag) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Payment Sheets reversed successfully.',
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Payment Sheets cannot be reversed.',
        ], 422);
    }

}
