<?php

namespace Modules\PaymentSheet\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;

class PaymentSheetController extends Controller
{
    private $paymentSheets;

     public function __construct(
        PaymentSheetRepository $paymentSheets,
    )
    {
        $this->paymentSheets = $paymentSheets;
    }

    public function update(Request $request, PaymentSheet $paymentSheet)
    {
        $inputs = [];

        if ($request->filled('deduction_amount')) {
            $inputs['deduction_amount'] = $request->deduction_amount;
            $inputs['paid_amount'] = $paymentSheet->net_amount - $request->deduction_amount;
        }
        if ($request->filled('deduction_remarks')) {
            $inputs['deduction_remarks'] = $request->deduction_remarks;
        }

        $paymentSheet->update($inputs);

        return response()->json([
            'paymentSheet'=> $paymentSheet,
            'status' => 'ok'
        ], 200);
    }
}
