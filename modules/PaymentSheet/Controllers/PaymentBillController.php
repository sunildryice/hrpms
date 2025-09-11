<?php

namespace Modules\PaymentSheet\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\BillCategoryRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\PaymentSheet\Repositories\PaymentBillRepository;
use Modules\PaymentSheet\Requests\PaymentBill\StoreRequest;
use Modules\PaymentSheet\Requests\PaymentBill\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Supplier\Repositories\SupplierRepository;

class PaymentBillController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        BillCategoryRepository $billCategories,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        PaymentBillRepository $paymentBills,
        ProjectCodeRepository $projectCodes,
        SupplierRepository $suppliers,
        UserRepository $users
    ) {
        $this->billCategories = $billCategories;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->paymentBills = $paymentBills;
        $this->projectCodes = $projectCodes;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->destinationPath = 'paymentBill';
    }

    /**
     * Display a listing of the payment bills
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->paymentBills->with(['office', 'supplier', 'requester', 'paymentSheetDetails'])
                ->whereOfficeId($authUser->employee->office_id)
                ->orderBy('bill_date', 'desc')->get();

            $dataTable = DataTables::of($data);

            return $dataTable->addIndexColumn()
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row) {
                    return $row->getSupplierVatPanNumber();
                })->addColumn('bill_date', function ($row) {
                    return $row->getBillDate();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('payment.bills.edit', $row->id).'" rel="tooltip" title="Edit Bill"><i class="bi-pencil-square"></i></a>';
                        if ($authUser->can('delete', $row)) {
                            $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                            $btn .= 'data-href="'.route('payment.bills.destroy', $row->id).'">';
                            $btn .= '<i class="bi-trash"></i></a>';
                        }
                    } else {
                        $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form"  href="';
                        $btn .= route('payment.bills.show', $row->id).'" rel="tooltip" title="Show Bill"><i class="bi-eye"></i></a>';
                        if (file_exists('storage/'.$row->attachment) && $row->attachment != '') {
                            $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                            $btn .= asset('storage/'.$row->attachment).'" target="_blank" rel="tooltip" title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>';
                        }
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::Bill.index');
    }

    /**
     * Show the form for creating a new payment bill by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $categories = $this->billCategories->getActiveCategories();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $suppliers = $this->suppliers->getActiveSuppliers();

        return view('PaymentSheet::Bill.create')
            ->withCategories($categories)
            ->withProjectCodes($projectCodes)
            ->withSuppliers($suppliers);
    }

    /**
     * Store a newly created payment bill in storage.
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
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', $inputs['bill_date'])
            ->where('end_date', '>=', $inputs['bill_date'])
            ->first();

        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['vat_amount'] = $inputs['vat_flag'] ? $inputs['bill_amount'] * config('constant.VAT_PERCENTAGE') / 100 : 0;
        $inputs['total_amount'] = $inputs['bill_amount'] + $inputs['vat_amount'];
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentBill = $this->paymentBills->create($inputs);

        if ($paymentBill) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath.'/'.$paymentBill->id, time().$request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
                $contract = $this->paymentBills->update($paymentBill->id, $inputs);
            }

            return redirect()->route('payment.bills.index')
                ->withSuccessMessage('Payment bill successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Payment bill can not be added.');
    }

    /**
     * Show the specified payment bill.
     *
     * @return mixed
     */
    public function show($paymentBillId)
    {
        $authUser = auth()->user();
        $paymentBill = $this->paymentBills->find($paymentBillId);

        return view('PaymentSheet::Bill.show')
            ->withPaymentBill($paymentBill);
    }

    /**
     * Show the form for editing the specified payment bill.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $paymentBill = $this->paymentBills->find($id);
        $this->authorize('update', $paymentBill);
        $categories = $this->billCategories->getActiveCategories();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $suppliers = $this->suppliers->getActiveSuppliers();

        return view('PaymentSheet::Bill.edit')
            ->withAuthUser($authUser)
            ->withCategories($categories)
            ->withPaymentBill($paymentBill)
            ->withProjectCodes($projectCodes)
            ->withSuppliers($suppliers);
    }

    /**
     * Update the specified payment bill in storage.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $paymentBill = $this->paymentBills->find($id);
        $this->authorize('update', $paymentBill);
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', $inputs['bill_date'])
            ->where('end_date', '>=', $inputs['bill_date'])
            ->first();

        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['vat_amount'] = $inputs['vat_flag'] ? $inputs['bill_amount'] * config('constant.VAT_PERCENTAGE') / 100 : 0;
        $inputs['total_amount'] = $inputs['bill_amount'] + $inputs['vat_amount'];
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath.'/'.$paymentBill->id, time().$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $paymentBill = $this->paymentBills->update($id, $inputs);

        if ($paymentBill) {
            $message = 'Payment bill is successfully updated.';

            return redirect()->route('payment.bills.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Payment bill can not be updated.');
    }

    /**
     * Remove the specified payment bill from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $paymentBill = $this->paymentBills->find($id);
        $this->authorize('delete', $paymentBill);
        $flag = $this->paymentBills->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Payment bill is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Payment bill can not deleted.',
        ], 422);
    }

    /**
     * Show the specified payment bill in printable view
     *
     * @return mixed
     */
    public function printBill($id)
    {
        $authUser = auth()->user();
        $paymentBill = $this->paymentBills->find($id);

        return view('PaymentSheet::Bill.print')
            ->withPaymentBill($paymentBill);
    }
}
