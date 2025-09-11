<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;

use Modules\Employee\Requests\DocumentUpload\UpdateRequest;

class DocumentUploadController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees
    )
    {
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee address in storage.
     *
     * @param \Modules\Employee\Requests\DocumentUpload\UpdateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UpdateRequest $request, $employeeId)
    {
        $employee = $this->employees->find($employeeId);
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
        return redirect()->back()->withInput()
                ->withSuccessMessage('Employee Documents successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee Documents can not be added.');
    }
}
