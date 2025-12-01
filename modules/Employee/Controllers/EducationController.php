<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Modules\Configuration\Repositories\OfficeRepository;
// use Modules\Configuration\Repositories\DepartmentRepository;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Requests\Education\StoreRequest;
use Modules\Employee\Requests\Education\UpdateRequest;

class EducationController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EducationRepository $education,
        protected EmployeeRepository $employees
    ) {
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
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
        $inputs['institution'] = $inputs['edu_institution'];
        unset($inputs['edu_institution']);
        $emp_education = $this->education->create($inputs);

        if ($emp_education) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_education.' . $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
            }

            $this->education->update($emp_education->id, $inputs);

            return redirect()->back()
                ->withSuccessMessage('Education details successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Education details can not be added.');
    }

    /**
     * Show the form for editing the specified employee education.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $education = $this->education->with(['employee'])->find($id);
        $attachment = '';
        if ($education->attachment != null) {
            $attachment = asset('storage/' . $education->attachment);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'education' => $education,
                'attachment' => $attachment,
                'updateAction' => route('employees.education.update', [$employeeId, $education->id]),
            ]);
        }

        return view('Employee::Education.edit')
            ->withEducation($education);
    }

    /**
     * Update the specified employee education in storage.
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $education = $this->education->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $education->employee_id, time() . '_education.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $education = $this->education->update($id, $inputs);

        if ($education) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee educational detail is successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee educational details can not be updated.');
    }
}
