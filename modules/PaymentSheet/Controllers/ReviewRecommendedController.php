<?php

namespace Modules\PaymentSheet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\PaymentSheet\Notifications\PaymentSheetReturned;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetVerified;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PaymentSheet\Requests\ReviewRecommended\StoreRequest;
use Yajra\DataTables\DataTables;

class ReviewRecommendedController extends Controller
{
    private $paymentSheets;

    /**
     * Create a new controller instance.
     *
     * @param PaymentSheetRepository $paymentSheets
     * @param UserRepository $users
     */
    public function __construct(
        PaymentSheetRepository  $paymentSheets,
    )
    {
        $this->paymentSheets = $paymentSheets;
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
            $data = $this->paymentSheets->with(['fiscalYear', 'status'])
                    ->select(['*'])
                    ->where('reviewer_id', $authUser->id)
                    ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS')]);

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
                    $btn = '';
                    if ($authUser->can('reviewRecommended', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('review.recommended.payment.sheets.create', $row->id) . '" rel="tooltip" title="Review Payment Sheet">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PaymentSheet::ReviewRecommended.index');
    }

    public function create($paymentSheetId)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $this->authorize('reviewRecommended', $paymentSheet);

        return view('PaymentSheet::ReviewRecommended.create')
            ->withAuthUser($authUser)
            ->withPaymentSheet($paymentSheet);
    }

    public function store(StoreRequest $request, $paymentSheetId)
    {
        $paymentSheet = $this->paymentSheets->find($paymentSheetId);
        $this->authorize('reviewRecommended', $paymentSheet);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $paymentSheet = $this->paymentSheets->reviewRecommended($paymentSheet->id, $inputs);

        if ($paymentSheet) {
            $message = '';
            if ($paymentSheet->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Payment sheet is successfully returned.';
                $paymentSheet->requester->notify(new PaymentSheetReturned($paymentSheet));
            } else if ($paymentSheet->status_id == config('constant.RECOMMENDED2_STATUS')) {
                $message = 'Payment sheet is successfully verified.';
                $paymentSheet->approver->notify(new PaymentSheetVerified($paymentSheet));
            }

            return redirect()->route('review.recommended.payment.sheets.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Payment sheet can not be verified.');
    }
}
