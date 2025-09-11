<?php

namespace Modules\PaymentSheet\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApprovedController extends Controller
{
    private $employees;

    private $fiscalYears;

    private $paymentSheets;

    private $users;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        PaymentSheetRepository $paymentSheets,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->paymentSheets = $paymentSheets;
        $this->users = $users;
    }

    /**
     * Display a listing of the payment sheets
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->paymentSheets->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row) {
                    return $row->getSupplierVatPanNumber();
                })->addColumn('payment_sheet_number', function ($row) {
                    return $row->getPaymentSheetNumber();
                })->addColumn('prepared_by', function ($row) {
                    return $row->requester->getFullName();
                })
                ->addColumn('submitted_date', function ($row) {
                    return $row->submittedDate();
                })
                ->addColumn('approved_date', function ($row) {
                    return $row->approvedDate();
                })
                ->addColumn('paid_date', function ($row) {
                    return $row->paidDate();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.payment.sheets.show', $row->id).'" rel="tooltip" title="View Payment Sheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.payment.sheets.print', $row->id).'" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    if ($authUser->can('pay', $row)) {
                        $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                        $btn .= route('approved.payment.sheets.pay.create', $row->id).'" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('PaymentSheet::Approved.index');
    }

    /**
     * Show the specified payment sheet.
     *
     * @return mixed
     */
    public function show($paymentRequestId)
    {
        $paymentRequest = $this->paymentSheets->find($paymentRequestId);
        $this->authorize('viewApproved', $paymentRequest);

        return view('PaymentSheet::Approved.show')
            ->withPaymentSheet($paymentRequest);
    }

    public function print($paymentSheetId)
    {
        $paymentRequest = $this->paymentSheets->find($paymentSheetId);

        $this->authorize('viewApproved', $paymentRequest);

        return view('PaymentSheet::Approved.print')
            ->withPaymentSheet($paymentRequest);
    }
}
