<?php

namespace Modules\Payroll\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Payroll\Repositories\PaymentItemRepository;
use Modules\Payroll\Repositories\PayrollBatchRepository;
use Modules\Payroll\Repositories\PayrollSheetDetailRepository;
use Modules\Payroll\Repositories\PayrollSheetRepository;

class SheetDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PayrollBatchRepository $payrollBatches
     * @param PayrollSheetRepository $payrollSheets
     * @param PaymentItemRepository $paymentItems
     */
    public function __construct(
        PayrollBatchRepository $payrollBatches,
        PayrollSheetRepository $payrollSheets,
        PayrollSheetDetailRepository $payrollSheetDetails,
        PaymentItemRepository  $paymentItems
    )
    {

        $this->payrollBatches = $payrollBatches;
        $this->payrollSheets = $payrollSheets;
        $this->payrollSheetDetails = $payrollSheetDetails;
        $this->paymentItems = $paymentItems;
    }

    /**
     *  Show the form for creating a new payment item within payment sheet.
     *
     * @param Request $request
     * @param $batchId
     * @param $sheetId
     * @return void
     */
    public function create(Request $request, $batchId, $sheetId)
    {
        $payrollBatch = $this->payrollBatches->find($batchId);
        $payrollSheet = $this->payrollSheets->find($sheetId);
        $paymentItems = $this->paymentItems->select(['*'])
            ->whereNotIn('slug', ['cit'])
            ->get();

        $paymentItemIds = $payrollSheet->details->pluck('payment_item_id')->toArray();
        $paymentItems = $paymentItems->filter(function ($paymentItem) use ($paymentItemIds){
           return !in_array($paymentItem->id, $paymentItemIds);
        });
        return view('Payroll::Sheet.Detail.create')
            ->withPaymentItems($paymentItems)
            ->withPayrollSheet($payrollSheet);
    }

    public function store(Request $request, $batchId, $sheetId)
    {
        $payrollSheet = $this->payrollSheets->find($sheetId);
        $inputs = $request->only('payment_item_id', 'amount');
        $inputs['payroll_sheet_id'] = $payrollSheet->id;
        $detail = $this->payrollSheetDetails->create($inputs);
        if ($detail) {
            return response()->json(['status' => 'ok',
                'detail' => $detail,
                'message' => 'Payment item is successfully added in payroll sheet.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment item can not be added in this payroll sheet.'], 422);
    }
}
