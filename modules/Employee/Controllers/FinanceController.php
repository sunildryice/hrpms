<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\FinanceRepository;
use Modules\Employee\Requests\Finance\StoreRequest;
use Modules\Employee\Requests\Finance\UpdateRequest;

class FinanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected FinanceRepository $finances,
    ) {}

    /**
     * Store a newly created employee finance in storage.
     *
     * @param  \Modules\Employee\Requests\Address\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $employeeId)
    {
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $inputs['disabled'] = $request->disabled ? 1 : 0;

        $employeeFinance = $this->finances->create($inputs);

        if ($employeeFinance) {
            return redirect()->back()
                ->withSuccessMessage('Employee details successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee details can not be added.');
    }

    /**
     * Update the specified finance in storage.
     *
     * @param  \Modules\Employee\Requests\Address\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $employeeFinance = $this->finances->find($id);
        $this->authorize('update', $employeeFinance);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['disabled'] = $request->disabled ? 1 : 0;
        $employeeFinance = $this->finances->update($id, $inputs);
        if ($employeeFinance) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee details successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee details can not be updated.');
    }
}
