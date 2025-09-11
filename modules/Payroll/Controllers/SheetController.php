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
use Modules\Privilege\Repositories\UserRepository;

class SheetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PayrollFiscalYearRepository $payrollFiscalYears
     * @param PayrollBatchRepository $payrollBatches
     * @param PayrollSheetRepository $payrollSheets
     * @param UserRepository $users
     */
    public function __construct(
        PayrollFiscalYearRepository $payrollFiscalYears,
        PayrollBatchRepository      $payrollBatches,
        PayrollSheetRepository      $payrollSheets,
        UserRepository $users
    )
    {
        $this->payrollFiscalYears = $payrollFiscalYears;
        $this->payrollBatches = $payrollBatches;
        $this->payrollSheets = $payrollSheets;
        $this->users = $users;
    }

    /**
     * Display a listing of the payroll batch sheets.
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function index(Request $request, $id)
    {
        $authUser = auth()->user();
        $payrollBatch = $this->payrollBatches->find($id);
        if ($request->ajax()) {
            $data = $this->payrollSheets->with(['employee'])
                ->where('payroll_batch_id', $payrollBatch->id)
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->employee->getFullNameWithCode();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('payroll.batches.sheets.edit', [$row->payroll_batch_id, $row->id]) . '" rel="tooltip" title="View Payroll Sheet"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }
        $reviewers = $this->users->permissionBasedUsers('verify-payroll');
        $approvers = $this->users->permissionBasedUsers('approve-payroll');
        if($reviewers){
            $reviewers = $reviewers->reject(function ($reviewer) use ($authUser){
                return $reviewer->id == $authUser->id;
            });
        }

        return view('Payroll::Sheet.index')
            ->withApprovers($approvers)
            ->withPayrollBatch($payrollBatch)
            ->withReviewers($reviewers);
    }

    /**
     * show payroll batch sheet
     *
     * @param $batchId
     * @param $id
     * @return mixed
     */
    public function show($batchId, $id)
    {
        $payrollBatch = $this->payrollBatches->find($batchId);
        $payrollSheet = $this->payrollSheets->with(['employee', 'payrollBatch', 'details.paymentItem'])->find($id);
        $benefitDetails = $payrollSheet->details->filter(function($detail){
            return $detail->paymentItem->type == 'B';
        });
        $deductionDetails = $payrollSheet->details->filter(function($detail){
            return $detail->paymentItem->type == 'D';
        });

        return view('Payroll::Sheet.show')
            ->withBenefitDetails($benefitDetails)
            ->withDeductionDetails($deductionDetails)
            ->withPayrollBatch($payrollBatch)
            ->withPayrollSheet($payrollSheet);
    }

    /**
     * show payroll batch sheet edit form
     *
     * @param $batchId
     * @param $id
     * @return mixed
     */
    public function edit($batchId, $id)
    {
        $payrollBatch = $this->payrollBatches->find($batchId);
        $payrollSheet = $this->payrollSheets->with(['employee', 'payrollBatch', 'details.paymentItem'])->find($id);
        $benefitDetails = $payrollSheet->details->filter(function($detail){
            return $detail->paymentItem->type == 'B';
        });
        $deductionDetails = $payrollSheet->details->filter(function($detail){
            return $detail->paymentItem->type == 'D';
        });

        return view('Payroll::Sheet.edit')
            ->withBenefitDetails($benefitDetails)
            ->withDeductionDetails($deductionDetails)
            ->withPayrollBatch($payrollBatch)
            ->withPayrollSheet($payrollSheet);
    }

    /**
     * Process the payment transactions
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reconcile($batchId, $id)
    {
        $payrollSheet = $this->payrollSheets->find($id);
        $this->payrollSheets->reconcile($payrollSheet->id);
        return redirect()->route('payroll.batches.sheets.show', $payrollSheet->id);
    }
}
