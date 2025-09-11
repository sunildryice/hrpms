<?php

namespace Modules\Report\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\Report\Exports\Finance\PaymentSheetExport;
use Yajra\DataTables\DataTables;

class PaymentSheetController extends Controller
{
    public function index(Request $request)
    {
        $data = PaymentSheet::query();
        $data->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);

        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                         ->whereDate('created_at', '<', $end_date);
                }
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('payment_sheet_number', function ($row){
                return $row->getPaymentSheetNumber();
            })
            ->addColumn('vendor', function ($row){
                return $row->getSupplierName();
            })
            ->addColumn('vat_pan_number', function ($row){
                return $row->getSupplierVatPanNumber();
            })
            ->addColumn('bill_number', function ($row){
                return $row->getPaymentBillNumber();
            })
            ->addColumn('bill_date', function ($row){
                return $row->getPaymentBillDate();
            })
            ->addColumn('purpose', function ($row){
                return $row->purpose;
            })
            ->addColumn('bill_amount', function ($row){
                return $row->total_amount;
            })
            ->addColumn('less_tds', function ($row){
                return $row->tds_amount;
            })
            ->addColumn('net_payment', function ($row){
                return $row->net_amount;
            })
            ->addColumn('office', function ($row){
                return $row->getOfficeName();
            })
            ->addColumn('approved_date', function ($row){
                return $row->getApprovedDate();
            })
            ->addColumn('voucher_reference_number', function ($row){
                return $row->voucher_reference_number ?: $row->payment_remarks;
            })
            ->addColumn('payment_status', function ($row){
                return $row->getPaymentStatus();
            })
            ->make(true);
        }

        return view('Report::Finance.PaymentSheet.index');
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;

        return new PaymentSheetExport($start_date, $end_date);
    }
}
