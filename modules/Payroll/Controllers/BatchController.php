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
use Modules\Payroll\Requests\StoreRequest;
use Modules\Payroll\Requests\UpdateRequest;

class BatchController extends Controller
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
                ->orderBy('posted_date', 'desc')->get();
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
                    $btn .= route('payroll.batches.sheets.index', $row->id) . '" rel="tooltip" title="View Payroll Batch"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('process', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('payroll.batches.process', $row->id) . '" rel="tooltip" title="Process Batch"><i class="bi-arrow-counterclockwise"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('payroll.batches.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('Payroll::Batch.index');
    }


    /**
     * Show the form for creating a new payroll batch
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        return view('Payroll::Batch.create')
            ->withFiscalYears($this->fiscalYears->get())
            ->withMonths($this->helper->getMonthArray());
    }

    /**
     * Store the newly created payment batch on storage
     *
     * @param Request $request
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['status_id'] = 1;
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $fiscalYear = $this->fiscalYears->find($inputs['fiscal_year_id']);
        $date = date('Y-m-d', strtotime($fiscalYear->title.'-'.$inputs['month'].'-01'));
        $payrollFiscalYear = $this->payrollFiscalYears->select(['*'])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();
        $inputs['payroll_fiscal_year_id'] = $payrollFiscalYear->id;
        $payrollBatch = $this->payrollBatches->create($inputs);

        if ($payrollBatch) {
            return redirect()->route('payroll.batches.index')
                ->withSuccessMessage('Payroll batch successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Payroll batch can not be added.');
    }

    /**
     * Store the newly created payment batch on storage
     *
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $payrollBatch = $this->payrollBatches->find($id);
        $this->authorize('submit', $payrollBatch);
        $inputs = $request->validated();
        $inputs['status_id'] = 3;
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $payrollBatch = $this->payrollBatches->update($id, $inputs);

        if ($payrollBatch) {
            return redirect()->route('payroll.batches.index')
                ->withSuccessMessage('Payroll batch successfully submitted.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Payroll batch can not be submitted.');
    }

    /**
     * Remove the specified office from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->payrollBatches->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Payroll batch is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Payroll batch can not deleted.',
        ], 422);
    }

    /**
     * Process the payment transactions
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process($id)
    {
        $payrollBatch = $this->payrollBatches->find($id);
        $employees = $this->employees->getActiveEmployees()->sortBy('id');
        $fiscalYear = $payrollBatch->fiscalYear;
        $checkDate = date('Y-m-d', strtotime($fiscalYear->title . '-' . $payrollBatch->month));

        foreach ($employees as $employee) {
            $paymentMaster = $this->paymentMasters->with(['paymentDetails'])
                ->where('start_date', '<=', $checkDate)
                ->where('end_date', '>=', $checkDate)
                ->where('employee_id', $employee->id)
                ->first();

            if ($paymentMaster) {
                if ($paymentMaster->paymentDetails->count()) {
                    $this->payrollSheets->createPayrollSheet([
                        'employee_id' => $employee->id,
                        'payroll_batch_id' => $payrollBatch->id,
                        'fiscal_year_id' => $fiscalYear->id,
                    ], $paymentMaster);
                }
            }
        }
        return redirect()->route('payroll.batches.sheets.index', $payrollBatch->id);
    }
}
