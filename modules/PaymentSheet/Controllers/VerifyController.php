<?php

namespace Modules\PaymentSheet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetVerified;
use Modules\PaymentSheet\Notifications\PaymentSheetReturned;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PaymentSheet\Requests\Verify\StoreRequest;

use DataTables;

class VerifyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PaymentSheetRepository $paymentSheets
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository     $employees,
        FiscalYearRepository   $fiscalYears,
        PaymentSheetRepository $paymentSheets,
        UserRepository         $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->paymentSheets = $paymentSheets;
        $this->users = $users;
        $this->destinationPath = 'paymentSheet';
    }

    /**
     * Display a listing of the Payment sheets
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->paymentSheets->with(['fiscalYear', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('verifier_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier', function ($row){
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row){
                    return $row->getSupplierVatPanNumber();
                })->addColumn('payment_sheet_number', function ($row){
                    return $row->getPaymentSheetNumber();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('verify.payment.sheets.create', $row->id) . '" rel="tooltip" title="Verify Payment Sheet">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::Verify.index');
    }

    public function create($paymentSheetId)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $this->authorize('verify', $paymentSheet);

        return view('PaymentSheet::Verify.create')
            ->withAuthUser($authUser)
            ->withPaymentSheet($paymentSheet);
    }

    public function store(StoreRequest $request, $paymentSheetId)
    {
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentSheet = $this->paymentSheets->verify($paymentSheet->id, $inputs);
        if ($paymentSheet) {
            $message = '';
            if ($paymentSheet->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Payment sheet is successfully returned.';
                 $paymentSheet->requester->notify(new paymentSheetReturned($paymentSheet));
            } else {
                $message = 'Payment sheet is successfully verified.';
                $paymentSheet->approver->notify(new paymentSheetVerified($paymentSheet));
            }
            return redirect()->route('verify.payment.sheets.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Payment sheet can not be verified.');
    }
}
