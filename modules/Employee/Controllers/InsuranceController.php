<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\InsuranceRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Requests\Insurance\StoreRequest;
use Modules\Employee\Requests\Insurance\UpdateRequest;

class InsuranceController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  InsuranceRepository $insurance
     * @return void
     */
    public function __construct(
        protected InsuranceRepository $insurance,
        protected EmployeeRepository $employees
    ) {
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee insurance in storage.
     *
     * @param  \Modules\Employee\Requests\Insurance\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $employeeId)
    {
        //        $this->authorize('manage-employee');
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $emp_insurance = $this->insurance->create($inputs);
        if ($emp_insurance) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_insurance.' . $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
            }

            $this->insurance->update($emp_insurance->id, $inputs);
            return redirect()->back()
                ->withSuccessMessage('Insurance details successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Insurance details can not be added.');
    }

    /**
     * Show the form for editing the specified employee insurance.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $insurance = $this->insurance->with(['employee'])->find($id);
        $attachment  = '';
        if ($insurance->attachment != NULL) {
            $attachment = asset('storage/' . $insurance->attachment);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'paid_date' => $insurance->paid_date ? $insurance->paid_date->format('Y-m-d') : '',
                'insurance' => $insurance,
                'attachment' => $attachment,
                'updateAction' => route('employees.insurance.update', [$employeeId, $insurance->id]),
            ]);
        }


        return view('Employee::Insurance.edit', [
            'actionMode' => $actionMode,
        ])
            ->withInsurance($insurance);
    }

    /**
     * Update the specified employee insurance in storage.
     *
     * @param \Modules\Employee\Requests\Insurance\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $insurance = $this->insurance->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $insurance->employee_id, time() . '_insurance.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $insurance = $this->insurance->update($id, $inputs);

        if ($insurance) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee insurance detail is successfully updated.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee insurance details can not be updated.');
    }
}
