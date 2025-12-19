<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Employee\Requests\Consultant\StoreRequest;
use Modules\Employee\Requests\Consultant\UpdateRequest;
use Modules\Master\Repositories\BloodGroupRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\EducationLevelRepository;
use Modules\Master\Repositories\FamilyRelationRepository;
use Modules\Master\Repositories\GenderRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\MaritalStatusRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Payroll\Repositories\PayrollFiscalYearRepository;
use Modules\Privilege\Repositories\RoleRepository;

class ConsultantController extends Controller
{
    protected $destinationPath;

    public function __construct(
        protected BloodGroupRepository        $bloodGroups,
        protected DepartmentRepository        $departments,
        protected DesignationRepository       $designations,
        protected DistrictRepository          $districts,
        protected EducationRepository         $education,
        protected EducationLevelRepository    $educationLevel,
        protected EmployeeRepository          $employees,
        protected FamilyRelationRepository    $familyRelations,
        protected GenderRepository            $genders,
        protected LeaveRepository             $leaves,
        protected LeaveTypeRepository         $leaveTypes,
        protected LocalLevelRepository        $localLevels,
        protected MaritalStatusRepository     $maritalStatus,
        protected OfficeRepository            $offices,
        protected PayrollFiscalYearRepository $payrollFiscalYears,
        protected ProvinceRepository          $provinces,
        protected RoleRepository              $roles
    )
    {
        $this->destinationPath = 'consultant';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $query = $this->employees->with(['user', 'department', 'designation', 'office', 'latestTenure.dutyStation', 'latestTenure.supervisor'])
                ->select(['*'])
                ->whereIn('employee_type_id', [config('constant.FULL_TIME_CONSULTANT'), config('constant.PART_TIME_CONSULTANT')]);
            if ($request->active) {
                $query->whereNotNull('activated_at');
            } else {
                $query->whereNull('activated_at');
            }
            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_code', function ($employee) {
                    return $employee->requestSTEId;
                })->addColumn('official_email_address', function ($employee) {
                    return $employee->user?->email_address;
                })->addColumn('position', function ($employee) {
                    return $employee->getDesignationName();
                })->addColumn('department', function ($employee) {
                    return $employee->getDepartmentName();
                })->addColumn('supervisor', function ($employee) {
                    return $employee->getSupervisorName();
                })->addColumn('duty_station', function ($employee) {
                    return $employee->getDutyStation();
                })->addColumn('status', function ($employee) {
                    return $employee->getActiveStatus();
                })->addColumn('action', function ($employee) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('consultant.profile', [$employee->id]) . '" rel="tooltip" title="View Consultant Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $employee)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('consultant.edit', $employee->id) . '" rel="tooltip" title="Edit Consultant"><i class="bi-pencil-square"></i></a>';
                    }
                    // if ($authUser->can('payroll')) {
                    //     $btn .= '&emsp;<a class="btn btn-success btn-sm" href="';
                    //     $btn .= route('employees.payments.masters.index', $employee->id).'" rel="tooltip" title="Payment Masters"><i class="bi bi-cash-coin"></i></a>';
                    // }

                    return $btn;
                })->rawColumns(['action', 'position'])
                ->make(true);
        }

        return view('Employee::Consultant.index')
            ->withRequestData($request->all());
    }

    public function create()
    {
        return view('Employee::Consultant.create')
            ->withGenders($this->genders->get())
            ->withMaritalStatus($this->maritalStatus->get());
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inputs['employee_type_id'] = config('constant.FULL_TIME_CONSULTANT');
        $employee = $this->employees->create($inputs);
        if ($employee) {
            if ($request->file('citizenship_attachment')) {
                $filename = $request->file('citizenship_attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_citizenship.' . $request->file('citizenship_attachment')->getClientOriginalExtension());
                $inputs['citizenship_attachment'] = $filename;
            }

            if ($request->file('pan_attachment')) {
                $filename = $request->file('pan_attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_pan.' . $request->file('pan_attachment')->getClientOriginalExtension());
                $inputs['pan_attachment'] = $filename;
            }
            $this->employees->update($employee->id, $inputs);

            return redirect()->route('consultant.edit', $employee->id)
                ->withSuccessMessage('Consultant successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Consultant can not be added.');
    }

    public function edit($id)
    {
        $employee = $this->employees->with([
            'medicalCondition',
            'tenures.designation',
            'tenures.department',
            'tenures.supervisor',
            'tenures.crossSupervisor',
            'tenures.nextLineManager',
            'tenures.dutyStation',
            'trainings',
            'address',
            'experiences',
        ])->find($id);
        $supervisors = $this->employees->select(['id', 'full_name', 'official_email_address'])
            ->where('id', '<>', $employee->id)
            // ->whereNotNull('activated_at')
            ->orderBy('full_name', 'asc')
            ->get();

        $actionMode = 'edit';
        return view('Employee::Consultant.edit')
            ->withActionMode($actionMode)
            ->withAuthUser(auth()->user())
            ->withBloodGroups($this->bloodGroups->get())
            ->withDepartments($this->departments->orderby('title', 'asc')->get())
            ->withDesignations($this->designations->orderby('title', 'asc')->get())
            ->withDistricts($this->districts->getDistricts())
            ->withDutyStations($this->districts->getEnabledDistricts())
            ->withEducation($this->education->get())
            ->withEducationLevels($this->educationLevel->get())
            ->withDepartments($this->departments->orderby('title', 'asc')->get())
            ->withDesignations($this->designations->orderby('title', 'asc')->get())
            ->withEmployee($employee)
            ->withFamilyRelations($this->familyRelations->orderby('position', 'asc')->get())
            ->withGenders($this->genders->get())
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withMaritalStatus($this->maritalStatus->get())
            ->withOffices($this->offices->select(['*'])->whereNotNull('activated_at')->get())
            ->withPayrollFiscalYears($this->payrollFiscalYears->get())
            ->withProvinces($this->provinces->get())
            ->withRoles($this->roles->where('id', '<>', 1)->orderby('role', 'asc')->get())
            ->withSupervisors($supervisors)
            ->withTenure($employee->latestTenure);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param \Modules\Employee\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $employee = $this->employees->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : null;
        if ($request->active && $employee->activated_at) {
            unset($inputs['activated_at']);
        }

        if ($request->file('citizenship_attachment')) {
            $filename = $request->file('citizenship_attachment')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_citizenship.' . $request->file('citizenship_attachment')->getClientOriginalExtension());
            $inputs['citizenship_attachment'] = $filename;
        }

        if ($request->file('pan_attachment')) {
            $filename = $request->file('pan_attachment')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_pan.' . $request->file('pan_attachment')->getClientOriginalExtension());
            $inputs['pan_attachment'] = $filename;
        }
        $employee = $this->employees->update($id, $inputs);
        if ($employee) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Consultant successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Consultant can not be updated.');
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->employees->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Consultant is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Consultant can not deleted.',
        ], 422);
    }

    public function profile($id)
    {
        $employee = $this->employees->find($id);

        $leaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', date('Y'))
            ->get();
        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())
            ->where('leave_frequency', 2)
            ->get();
        $prevLeaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', '<>', date('Y'))
            // ->where( DB::raw('YEAR(reported_date)'), '<>', date('Y') )
            ->get();

        $view = view('Employee::Consultant.profile');
        if ($employee->user) {
            $leaveRequests = $this->employees->getLeaveRequestsOfCurrentAndPreviousFiscalYear($employee->id);
            $leaveEncashments = $this->employees->getLeaveEncashRequestsOfCurrentAndPreviousFiscalYear($employee->id);
            $view->with([
                'leaveRequests' => $leaveRequests,
                'leaveEncashments' => $leaveEncashments,
                'previousLeaves' => $prevLeaves,
            ]);
        }

        return $view->with([
            'authUser' => auth()->user(),
            'employee' => $employee,
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
        ]);
    }
}
