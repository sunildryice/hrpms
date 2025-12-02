<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\MedicalConditionRepository;
use Modules\Employee\Requests\MedicalCondition\StoreRequest;
use Modules\Employee\Requests\MedicalCondition\UpdateRequest;

class MedicalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected MedicalConditionRepository $medicalCondition
    ) {}

    /**
     * Store a newly created employee education in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $employeeId)
    {
        //        $this->authorize('manage-employee');
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $medical = $employee->medicalCondition()->create($inputs);
        if ($medical) {
            return redirect()->back()
                ->withSuccessMessage('Employee medical condition is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee medical condition can not be added.');
    }

    /**
     * Update the specified employee medical condition in storage.
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $this->medicalCondition->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $medicalCondition = $this->medicalCondition->update($id, $inputs);

        if ($medicalCondition) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee medical condition is successfully updated.');
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Employee medical condition can not be updated.');
    }
}
