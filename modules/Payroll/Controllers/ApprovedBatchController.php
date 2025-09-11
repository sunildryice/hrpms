<?php

namespace Modules\Payroll\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payroll\Repositories\PayrollBatchRepository;
use Yajra\DataTables\DataTables;

class ApprovedBatchController extends Controller
{
    private $payrollBatches;
    public function __construct(
        PayrollBatchRepository $payrollBatches
    )
    {
        $this->payrollBatches = $payrollBatches;        
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->payrollBatches->getApproved();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('created_by', function ($row) {
                return $row->getCreatedBy();
            })->addColumn('fiscal_year', function ($row) {
                return $row->getFiscalYear();
            })->addColumn('month', function ($row) {
                return $row->getMonth();
            })->addColumn('posted_date', function ($row) {
                return $row->getPostedDate();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('payroll.batches.sheets.index', $row->id) . '" rel="tooltip" title="View Payroll Batch"><i class="bi bi-eye"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('Payroll::Batch.Approved.index');
    }
}