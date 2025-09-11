<?php

namespace Modules\Report\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\FundRequest\Models\FundRequest;
use Modules\Report\Exports\Finance\FundRequestExport;
use Yajra\DataTables\DataTables;

class FundRequestController extends Controller
{
    public function index(Request $request)
    {        
        $data = FundRequest::query();
        $data->where('status_id', config('constant.APPROVED_STATUS'));
        
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
            ->addColumn('fund_request_number', function ($row){
                return $row->getFundRequestNumber();
            })
            ->addColumn('requested_by', function ($row){
                return $row->getRequesterName();
            })
            ->addColumn('fiscal_year', function ($row){
                return $row->getFiscalYear();
            })
            ->addColumn('month', function ($row){
                return $row->getMonthName();
            })
            ->addColumn('district', function ($row){
                return $row->getDistrictName();
            })
            ->addColumn('project', function ($row){
                return $row->getProjectCode();
            })
            ->addColumn('requested_date', function ($row){
                return $row->created_at->format('M d, Y');
            })
            ->addColumn('remarks', function ($row){
                return $row->remarks;
            })
            ->make(true);
        }

        return view('Report::Finance.FundRequest.index');
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;

        return new FundRequestExport($start_date, $end_date);
    }
}
