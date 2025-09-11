<?php

namespace Modules\Report\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\AdvanceRequest\Models\AdvanceRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\Finance\AdvanceRequestExport;
use Yajra\DataTables\DataTables;

class AdvanceRequestController extends Controller
{
    public function __construct(
        EmployeeRepository  $employees,
        OfficeRepository    $offices
    )
    {
        $this->employees    = $employees;
        $this->offices      = $offices;
    }
    public function index(Request $request)
    {        
        $data = AdvanceRequest::query();
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

            if ($request->has('requester') && $request->requester) {
                $requesterUserId = $request->requester;
                $data->where('requester_id', $requesterUserId);
            }

            if ($request->has('office') && $request->office) {
                $officeId = $request->office;
                $data->where('office_id', $officeId);
            }

            if ($request->has('status') && $request->status) {

            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('cash_advance_number', function ($row){
                return $row->getAdvanceRequestNumber();
            })
            ->addColumn('requester', function ($row){
                return $row->getRequesterName();
            })
            ->addColumn('office', function ($row){
                return $row->office->office_name;
            })
            ->addColumn('purpose', function ($row){
                return $row->purpose;
            })
            ->addColumn('advance_amount', function ($row){
                return $row->getEstimatedAmount();
            })
            ->addColumn('advance_released_date', function ($row){
                return '';
            })
            ->addColumn('program_completion_date', function ($row){
                return $row->end_date->toFormattedDateString();
            })
            ->addColumn('settlement_date', function ($row){
                return $row->getSettlementDate();
            })
            ->addColumn('settled_amount', function ($row){
                return '';
            })
            ->addColumn('balance', function ($row){
                return '';
            })
            ->addColumn('status', function ($row){
                return $row->getStatus();
            })
            ->make(true);
        }

        $array = [
            'employees' => $this->employees->getActiveEmployees(),
            'offices'   => $this->offices->getOffices()
        ];

        return view('Report::Finance.AdvanceRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;

        return new AdvanceRequestExport($start_date, $end_date);
    }
}
