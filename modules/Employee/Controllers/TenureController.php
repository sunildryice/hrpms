<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\TenureRepository;
use Modules\Employee\Requests\Tenure\StoreRequest;
use Modules\Employee\Requests\Tenure\UpdateRequest;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\OfficeRepository;

class TenureController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected DepartmentRepository $departments,
        protected DesignationRepository $designations,
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected OfficeRepository $offices,
        protected TenureRepository $tenures
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
    public function store(StoreRequest $request, $employee)
    {
        $employee = $this->employees->find($employee);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        $family = $this->tenures->create($inputs);
        if ($family) {
            return redirect()->back()
                ->withSuccessMessage('Employee tenure detail is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee tenure detail can not be added.');
    }

    /**
     * Show the form for editing the specified employee family member.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $tenure = $this->tenures->with(['employee'])->find($id);
        if ($request->wantsJson()) {
            return response()->json([
                'tenure' => $tenure,
                'joined_date' => $tenure->joined_date ? $tenure->joined_date->format('Y-m-d') : '',
                'to_date' => $tenure->getFormattedToDate(),
                'updateAction' => route('employees.tenures.update', [$tenure->employee_id, $tenure->id]),
            ]);
        }

        $supervisors = $this->employees->select(['id', 'full_name', 'official_email_address'])
            ->where('id', '<>', $tenure->employee_id)
            ->whereNotNull('activated_at')
            ->orderBy('full_name', 'asc')
            ->get();

        return view('Employee::Tenure.edit')
            ->withDepartments($this->departments->getActiveDepartments())
            ->withDesignations($this->designations->getActiveDesignations())
            ->withDistricts($this->districts->getDistricts())
            ->withOffices($this->offices->getActiveOffices())
            ->withSupervisors($supervisors)
            ->withTenure($tenure);
    }

    /**
     * Update the specified employee tenure in storage.
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $tenure = $this->tenures->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $tenure = $this->tenures->update($id, $inputs);

        if ($tenure) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee tenure detail is successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee tenure can not be updated.');
    }
}
