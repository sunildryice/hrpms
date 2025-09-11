<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\PaymentMasterRepository;
use Modules\Employee\Requests\PaymentMaster\StoreRequest;
use Modules\Employee\Requests\PaymentMaster\UpdateRequest;

use DataTables;
use Modules\Payroll\Repositories\PaymentItemRepository;

class PaymentMasterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  PaymentItemRepository $paymentItems
     * @param  PaymentMasterRepository $paymentMasters
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        PaymentItemRepository $paymentItems,
        PaymentMasterRepository $paymentMasters,
    )
    {
        $this->employees   = $employees;
        $this->paymentItems   = $paymentItems;
        $this->paymentMasters   = $paymentMasters;
    }

    /**
     * Display a listing of the activity code.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $id)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($id);
        if ($request->ajax()) {
            $data = $this->paymentMasters->select(['*'])
                ->whereEmployeeId($id)
                ->orderBy('start_date')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('employees.payments.masters.details.index', [$row->id]) . '"><i class="bi-eye"></i></a>';
                    if($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-payment-modal-form" href="';
                        $btn .= route('employees.payments.masters.edit', [$row->employee_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('employees.payments.masters.destroy', [$row->employee_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })->addColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Employee::PaymentMaster.index')
            ->withEmployee($employee);
    }

    public function create($employeeId)
    {
        $employee = $this->employees->find($employeeId);
        return view('Employee::PaymentMaster.create')
            ->withEmployee($employee);
    }

    /**
     * Store a newly created employee payment master in storage.
     *
     * @param StoreRequest $request
     * @param $employeeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $employeeId)
    {
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();

        $paymentMaster = $this->paymentMasters->select(['*'])
            ->where('employee_id', $employeeId)
            ->where(function($q) use ($inputs){
                $q->where(function ($query) use ($inputs){
                    $query->whereDate('start_date', '<=',$inputs['start_date'])
                        ->whereDate('end_date', '>=',$inputs['start_date']);
                })->orWhere(function ($query) use ($inputs){
                    $query->whereDate('start_date', '<=',$inputs['end_date'])
                        ->whereDate('end_date', '>=',$inputs['end_date']);
                })->orWhere(function ($query) use ($inputs){
                    $query->whereDate('start_date', '>',$inputs['start_date'])
                        ->whereDate('end_date', '<',$inputs['end_date']);
                });
            })->first();

        if($paymentMaster){
            return response()->json(['status' => 'error',
                'message' => 'Payment master overlaps for selected date range.'], 422);
        }

        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $paymentMaster = $this->paymentMasters->create($inputs);
        if ($paymentMaster) {
            return response()->json(['status' => 'ok',
                'payment_master' => $paymentMaster,
                'message' => 'Payment master is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment master can not be added.'], 422);
    }

    public function show($employeeId, $id)
    {
        $employee = $this->employees->find($employeeId);
        $paymentMaster = $this->paymentMasters->find($id);
        return view('Employee::PaymentMaster.show')
            ->withEmployee($employee)
            ->withPaymentMaster($paymentMaster);
    }

    public function edit($employeeId, $id)
    {
        $employee = $this->employees->find($employeeId);
        $paymentMaster = $this->paymentMasters->find($id);
        return view('Employee::PaymentMaster.edit')
            ->withEmployee($employee)
            ->withPaymentMaster($paymentMaster);
    }

    /**
     * Update the specified payment master in storage.
     *
     * @param UpdateRequest $request
     * @param $employeeId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $paymentMaster = $this->paymentMasters->find($id);
//        $this->authorize('update', $paymentMaster);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $paymentMaster = $this->paymentMasters->update($id, $inputs);
        if ($paymentMaster) {
            return response()->json(['status' => 'ok',
                'payment_master' => $paymentMaster,
                'message' => 'Payment master is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment master can not be updated.'], 422);
    }

    /**
     * Remove the specified fund request from storage.
     *
     * @param $employeeId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($employeeId, $id)
    {
        $paymentMaster = $this->paymentMasters->find($id);
//        $this->authorize('delete', $paymentMaster);
        $flag = $this->paymentMasters->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Payment master is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Payment master can not deleted.',
        ], 422);
    }
}
