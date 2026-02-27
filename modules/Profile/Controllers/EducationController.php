<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// use Modules\Configuration\Repositories\OfficeRepository;
// use Modules\Configuration\Repositories\DepartmentRepository;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Profile\Requests\Education\StoreRequest;
use Modules\Profile\Requests\Education\UpdateRequest;

class EducationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  EducationRepository $education
     * @return void
     */
    public function __construct(
        EducationRepository $education,
        EmployeeRepository $employees
    ) {
        $this->education = $education;
        $this->employees = $employees;
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @param  \Modules\Employee\Requests\Education\StoreRequest $request
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
        $emp_education = $this->education->create($inputs);
        if ($emp_education) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_education.' . $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
            }

            $this->education->update($emp_education->id, $inputs);
            return redirect()->route('profile.edit', ['tab' => 'education-details'])
                ->withSuccessMessage('Education details successfully added.');
        }
        return redirect()->route('profile.edit', ['tab' => 'education-details'])->withInput()
            ->withWarningMessage('Education details can not be added.');
    }

    /**
     * Show the form for editing the specified employee education.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
        $education = $this->education->with(['employee'])->find($id);
        $attachment  = '';
        if ($education->attachment != NULL) {
            $attachment = asset('storage/' . $education->attachment);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'education' => $education,
                'attachment' => $attachment,
                'updateAction' => route('profile.education.update', [$education->id]),
            ]);
        }

        return view('Profile::Education.edit')
            ->withEducation($education);
    }

    /**
     * Update the specified employee education in storage.
     *
     * @param \Modules\Employee\Requests\Education\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
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
            return redirect()->route('profile.edit', ['tab' => 'education-details'])
                ->withSuccessMessage('Educational detail is successfully updated.');
        }
        return redirect()->route('profile.edit', ['tab' => 'education-details'])
            ->withInput()
            ->withWarningMessage('Educational details can not be updated.');
    }
}
