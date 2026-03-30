<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\EmployeeSocialMediaRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Employee\Requests\StoreRequest;
use Modules\Employee\Requests\UpdateRequest;
use Modules\Master\Models\VehicleLicenseCategory;
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
use Modules\Master\Repositories\SocialMediaAccountRepository;
use Modules\Privilege\Repositories\RoleRepository;

class EmployeeController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected BloodGroupRepository $bloodGroups,
        protected DepartmentRepository $departments,
        protected DesignationRepository $designations,
        protected DistrictRepository $districts,
        protected EducationRepository $education,
        protected EducationLevelRepository $educationLevel,
        protected EmployeeRepository $employees,
        protected FamilyRelationRepository $familyRelations,
        protected GenderRepository $genders,
        protected LeaveRepository $leaves,
        protected LeaveTypeRepository $leaveTypes,
        protected LocalLevelRepository $localLevels,
        protected MaritalStatusRepository $maritalStatus,
        protected OfficeRepository $offices,
        protected ProvinceRepository $provinces,
        protected RoleRepository $roles,
        protected SocialMediaAccountRepository $socialMediaAccounts,
        protected EmployeeSocialMediaRepository $employeeSocialMediaRepository
    ) {
        $this->destinationPath = 'employees';
    }

    /**
     * Display a listing of the employee.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $query = $this->employees->with(['department', 'designation', 'office', 'latestTenure.dutyStation', 'latestTenure.supervisor'])
                ->select(['*'])->where(function ($q) {
                    $q->whereIn('employee_type_id', [config('constant.FULL_TIME_EMPLOYEE')]);
                    $q->orWhereNull('employee_type_id');
                });
            if ($request->active) {
                $query->whereNotNull('activated_at');
            } else {
                $query->whereNull('activated_at');
            }
            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_code', function ($employee) {
                    return $employee->request_id;
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
                    $btn .= route('employees.profile', [$employee->id]) . '" rel="tooltip" title="View Employee Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $employee)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('employees.edit', $employee->id) . '" rel="tooltip" title="Edit Employee"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'position'])
                ->make(true);
        }

        return view('Employee::index')
            ->withRequestData($request->all());
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $socialMediaAccounts = $this->socialMediaAccounts->get();
        return view('Employee::create', [
            'authUser' => $authUser,
            'socialMediaAccounts' => $socialMediaAccounts,
        ])
            ->withGenders($this->genders->get())
            ->withMaritalStatus($this->maritalStatus->get())
            ->withVehicleLicenseCategories(VehicleLicenseCategory::active()->orderBy('code')->get());
    }

    /**
     * Store a newly created employee in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
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

            if ($request->file('passport_attachment')) {
                $filename = $request->file('passport_attachment')
                    ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_passport.' . $request->file('passport_attachment')->getClientOriginalExtension());
                $inputs['passport_attachment'] = $filename;
            }
            $this->employees->update($employee->id, $inputs);

            return redirect()->route('employees.edit', $employee->id)
                ->withSuccessMessage('Employee successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee can not be added.');
    }

    /**
     * Display the specified employee.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $employee = $this->employees->find($id);


        return response()->json([
            'employee' => $employee,
        ], 200);
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
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
            'finance',
            'experiences',
        ])->find($id);
        $supervisors = $this->employees->select(['id', 'full_name', 'official_email_address'])
            ->where('id', '<>', $employee->id)
            ->whereNotNull('activated_at')
            ->orderBy('full_name', 'asc')
            ->get();

        $employeeSocialMediaLinks = $this->employeeSocialMediaRepository
            ->getSocialMediaLinksByEmployeeId($employee->id)
            ->pluck('link', 'title');

        $actionMode = 'edit';

        return view('Employee::edit', [
            'actionMode' => $actionMode,
        ])
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
            ->withHour($employee->latestHour)
            ->withTenure($employee->latestTenure)
            ->withSocialMediaAccounts($this->socialMediaAccounts->get())
            ->withEmployeeSocialMediaLinks($employeeSocialMediaLinks)
            ->withVehicleLicenseCategories(VehicleLicenseCategory::active()->orderBy('code')->get());
    }

    /**
     * Update the specified employee in storage.
     *
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

        if ($request->file('passport_attachment')) {
            $filename = $request->file('passport_attachment')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_passport.' . $request->file('passport_attachment')->getClientOriginalExtension());
            $inputs['passport_attachment'] = $filename;
        }
        $employee = $this->employees->update($id, $inputs);
        if ($employee) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee can not be updated.');
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
                'message' => 'Employee is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Employee can not deleted.',
        ], 422);
    }

    /**
     * Get profile of an employee
     *
     * @return mixed
     */
    public function profile($id)
    {
        $employee = $this->employees->find($id);
        $employee->setRelation('tenures', $employee->tenures()->orderBy('joined_date', 'asc')->get());
        $leaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', date('Y'))
            ->get();
        $annualLeaveId = config('constant.ANNUAL_LEAVE');
        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())
            ->get()
            ->sortBy(function ($type) use ($annualLeaveId) {
                return $type->id === $annualLeaveId ? 0 : 1;
            });
        $prevLeaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', '<>', date('Y'))
            // ->where( DB::raw('YEAR(reported_date)'), '<>', date('Y') )
            ->get();


        $employeeSocialMediaLinks = $this->employeeSocialMediaRepository
            ->getSocialMediaLinksByEmployeeId($employee->id)
            ->pluck('link', 'title');

        $actionMode = 'show';

        $view = view('Employee::profile', [
            'actionMode' => $actionMode,
        ]);
        if ($employee->user) {
            $leaveRequests = $this->employees->getLeaveRequestsOfCurrentAndPreviousFiscalYear($employee->id);
            $leaveEncashments = $this->employees->getLeaveEncashRequestsOfCurrentAndPreviousFiscalYear($employee->id);
            $view->withLeaveRequests($leaveRequests)
                ->withPreviousLeaves($prevLeaves)
                ->withLeaveEncashments($leaveEncashments);
        }


        return $view
            ->withAuthUser(auth()->user())
            ->withEmployee($employee)
            ->withEmployeeSocialMediaLinks($employeeSocialMediaLinks)
            ->withSocialMediaAccounts($this->socialMediaAccounts->get())
            ->withLeaves($leaves)
            ->withLeaveTypes($leaveTypes);
    }

    public function info($employeeId)
    {
        $employee = $this->employees->find($employeeId);

        return view('Employee::Profile.info', compact('employee'));
    }
}
