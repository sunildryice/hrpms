<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;

use Modules\Profile\Requests\DocumentUpload\UpdateRequest;

class DocumentUploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees
    )
    {
        $this->employees = $employees;
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee address in storage.
     *
     * @param \Modules\Employee\Requests\DocumentUpload\UpdateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UpdateRequest $request)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
        $inputs = $request->validated();

        if ($request->file('signature')) {
            $filename = $request->file('signature')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_signature.' . $request->file('signature')->getClientOriginalExtension());
            $inputs['signature'] = $filename;
        }
        if ($request->file('profile_picture')) {
            $filename = $request->file('profile_picture')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_profile_picture.' . $request->file('profile_picture')->getClientOriginalExtension());
            $inputs['profile_picture'] = $filename;
        }

        $inputs['updated_by'] = auth()->id();
        $employeeDocument = $this->employees->update($employee->id, $inputs);
        if ($employeeDocument) {
            return redirect()->route('profile.edit', ['tab' => 'document-upload-details'])
                ->withSuccessMessage('Documents successfully added.');
        }
        return redirect()->route('profile.edit', ['tab' => 'document-upload-details'])->withInput()
            ->withWarningMessage('Documents can not be added.');
    }
}
