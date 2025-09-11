<?php

namespace Modules\PaymentSheet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetApproved;
use Modules\PaymentSheet\Notifications\PaymentSheetRecommended;
use Modules\PaymentSheet\Notifications\PaymentSheetRejected;
use Modules\PaymentSheet\Notifications\PaymentSheetReturned;
use Modules\PaymentSheet\Notifications\PaymentSheetSubmitted;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PaymentSheet\Requests\Approve\StoreRequest;
use Yajra\DataTables\DataTables;

class ApproveController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $paymentSheets;
    private $users;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PaymentSheetRepository $paymentSheets
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        PaymentSheetRepository $paymentSheets,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->paymentSheets = $paymentSheets;
        $this->users = $users;
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
            $data = $this->paymentSheets->with(['fiscalYear', 'status'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')]);

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
                    $btn .= route('approve.payment.sheets.create', $row->id) . '" rel="tooltip" title="Approve Payment Sheet">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::Approve.index');
    }

    public function create($paymentSheetId)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $this->authorize('approve', $paymentSheet);

        $reviewers = $this->users->permissionBasedUsers('review-recommended-payment-sheet');
        $approvers = $this->users->permissionBasedUsers('approve-recommended-payment-sheet');

        return view('PaymentSheet::Approve.create')
            ->withApprovers($approvers)
            ->withAuthUser($authUser)
            ->withPaymentSheet($paymentSheet)
            ->withReviewers($reviewers);
    }

    public function store(StoreRequest $request, $paymentSheetId)
    {
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $this->authorize('approve', $paymentSheet);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentSheet = $this->paymentSheets->approve($paymentSheet->id, $inputs);

        if ($paymentSheet) {
            $message = '';
            if ($paymentSheet->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Payment sheet is successfully returned.';
                $paymentSheet->requester->notify(new PaymentSheetReturned($paymentSheet));
            } else if ($paymentSheet->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Payment sheet is successfully rejected.';
                $paymentSheet->requester->notify(new PaymentSheetRejected($paymentSheet));
            } else if ($paymentSheet->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Payment sheet is successfully recommended.';
                $paymentSheet->reviewer->notify(new PaymentSheetRecommended($paymentSheet));
            } else if ($paymentSheet->status_id == config('constant.RECOMMENDED2_STATUS')) {
                $message = 'Payment sheet is successfully recommended.';
                $paymentSheet->approver->notify(new PaymentSheetRecommended($paymentSheet));
            } else if ($paymentSheet->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Payment sheet is successfully approved.';
                $paymentSheet->requester->notify(new PaymentSheetApproved($paymentSheet));
            }

            return redirect()->route('approve.payment.sheets.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Payment sheet can not be approved.');
    }
}
