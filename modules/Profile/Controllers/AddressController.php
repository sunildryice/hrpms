<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\AddressRepository;
use Modules\Employee\Repositories\EmployeeRepository;

use Modules\Profile\Requests\Address\StoreRequest;
use Modules\Profile\Requests\Address\UpdateRequest;

class AddressController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  AddressRepository $address
     * @param  EmployeeRepository $employees
     * @return void
     */
    public function __construct(
        AddressRepository $address,
        EmployeeRepository $employees,
    )
    {
        $this->address     = $address;
        $this->employees   = $employees;
    }

    /**
     * Store a newly created employee address in storage.
     *
     * @param StoreRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = $authUser->id;
        if($request->validationcurrent){
            $inputs['permanent_province_id'] = $inputs['temporary_province_id'];
            $inputs['permanent_district_id'] = $inputs['temporary_district_id'];
            $inputs['permanent_local_level_id'] = $inputs['temporary_local_level_id'];
            $inputs['permanent_ward'] = $inputs['temporary_ward'];
            $inputs['permanent_tole'] = $inputs['temporary_tole'];
        }
        $employeeAddress = $this->address->create($inputs);
        if($employeeAddress){
            return redirect()->route('profile.edit', ['tab'=>'family-details'])
                ->withSuccessMessage('Address successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Address can not be added.');
    }

    /**
     * Update the specified address in storage.
     *
     * @param  \Modules\Employee\Requests\Address\UpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $employeeAddress = $this->address->find($id);
//        $this->authorize('update', $employeeAddress);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        if($request->validationcurrent){
            $inputs['permanent_province_id'] = $inputs['temporary_province_id'];
            $inputs['permanent_district_id'] = $inputs['temporary_district_id'];
            $inputs['permanent_local_level_id'] = $inputs['temporary_local_level_id'];
            $inputs['permanent_ward'] = $inputs['temporary_ward'];
            $inputs['permanent_tole'] = $inputs['temporary_tole'];
        }
        $employeeAddress = $this->address->update($id, $inputs);
        if($employeeAddress){
            return redirect()->route('profile.edit', ['tab'=>'family-details'])
                ->withSuccessMessage('Address successfully updated.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Address can not be updated.');
    }
}
