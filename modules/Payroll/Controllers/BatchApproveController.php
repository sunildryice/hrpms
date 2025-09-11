<?php

namespace Modules\Payroll\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\PaymentMasterRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Payroll\Repositories\PayrollBatchRepository;
use Modules\Payroll\Repositories\PayrollFiscalYearRepository;
use Modules\Payroll\Repositories\PayrollSheetRepository;
use Modules\Payroll\Requests\Approve\StoreRequest;

class BatchApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param Helper $helper
     * @param PaymentMasterRepository $paymentMasters
     * @param PayrollFiscalYearRepository $payrollFiscalYears
     * @param PayrollBatchRepository $payrollBatches
     */
    public function __construct(
        EmployeeRepository          $employees,
        FiscalYearRepository        $fiscalYears,
        Helper                      $helper,
        PaymentMasterRepository     $paymentMasters,
        PayrollFiscalYearRepository $payrollFiscalYears,
        PayrollBatchRepository      $payrollBatches,
        PayrollSheetRepository      $payrollSheets,
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->helper = $helper;
        $this->paymentMasters = $paymentMasters;
        $this->payrollFiscalYears = $payrollFiscalYears;
        $this->payrollBatches = $payrollBatches;
        $this->payrollSheets = $payrollSheets;
    }

    /**
     * Display a listing of the payroll batches.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $data = $this->payrollBatches->with(['status'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.VERIFIED_STATUS'));
                })->orderBy('posted_date', 'desc')->get();
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
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('payroll.batches.approve.create', $row->id) . '" rel="tooltip" title="Approve Payroll Batch"><i class="bi bi-hand-thumbs-up"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('Payroll::Batch.Approve.index');
    }


    /**
     * Show the form for creating a new payroll batch
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $payrollBatch = $this->payrollBatches->find($id);
        $authUser->can('approve', $payrollBatch);

        return view('Payroll::Batch.Approve.create')
            ->withPayrollBatch($payrollBatch);
    }

    /**
     * Store the newly created payment batch on storage
     *
     * @param Request $request
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $payrollBatch = $this->payrollBatches->find($id);
        $authUser->can('approve', $payrollBatch);

        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $payrollBatch = $this->payrollBatches->approve($id, $inputs);

        if ($payrollBatch) {
            return redirect()->route('payroll.batches.approve.index')
                ->withSuccessMessage('Payroll batch successfully approveed.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Payroll batch can not be approveed.');
    }
}
