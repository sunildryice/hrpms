<?php

namespace Modules\PaymentSheet\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\PaymentSheet\Repositories\PaymentBillRepository;

class PaymentBillController extends Controller
{
    private $paymentBills;
    /**
     * Create a new controller instance.
     *
     * @param PaymentBillRepository $paymentBills
     */

     public function __construct(
        PaymentBillRepository $paymentBills,
    )
    {
        $this->paymentBills = $paymentBills;
    }

    /**
     * Show the specified payment bill.
     *
     * @param $paymentBillId
     * @return mixed
     */
    public function show($paymentBillId)
    {
        $paymentBill = $this->paymentBills->find($paymentBillId);

        return response()->json([
            'paymentBill'=> $paymentBill,
            'leftAmount' => round($paymentBill->bill_amount-$paymentBill->settled_amount,2),
            'vatTdsPercentage'=>config('constant.VAT_TDS_PERCENTAGE')
        ], 200);
    }
}
