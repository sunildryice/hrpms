<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\MedicalConditionRepository;
use Modules\Profile\Requests\MedicalCondition\StoreRequest;
use Modules\Profile\Requests\MedicalCondition\UpdateRequest;

class MedicalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  MedicalConditionRepository $medicalCondition
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        MedicalConditionRepository $medicalCondition
    )
    {
        $this->employees = $employees;
        $this->medicalCondition = $medicalCondition;
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @param  \Modules\Employee\Requests\MedicalCondition\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $medical = $employee->medicalCondition()->create($inputs);
        if($medical){
            return redirect()->route('profile.edit', ['tab'=>'education-details'])
                ->withSuccessMessage('Medical condition is successfully added.');
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Medical condition can not be added.');
    }

    /**
     * Update the specified employee medical condition in storage.
     *
     * @param \Modules\Employee\Requests\MedicalCondition\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->medicalCondition->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $medicalCondition = $this->medicalCondition->update($id, $inputs);

        if($medicalCondition){
            return redirect()->route('profile.edit', ['tab'=>'education-details'])
                ->withSuccessMessage('Medical condition is successfully updated.');
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Medical condition can not be updated.');
    }
}
