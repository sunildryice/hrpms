<?php

namespace Modules\Profile\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Inventory\Repositories\AssetAssignLogRepository;
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
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Profile\Requests\UpdateRequest;

class EmployeeController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AssetAssignLogRepository $assetAssignLogs,
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
        protected RoleRepository $roles
    ) {
        $this->destinationPath = 'employees';
    }

    public function show()
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
        $leaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', date('Y'))
            ->get();
        $prevLeaves = $this->leaves->select('*')
            ->where('employee_id', $employee->id)
            ->whereYear('reported_date', '<>', date('Y'))
                    // ->where( DB::raw('YEAR(reported_date)'), '<>', date('Y') )
            ->get();

        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())
            ->when($employee->isConsultant(), function ($q) {
                $q->where('leave_frequency', 2);
            })->get();

        $leaveRequests = $this->employees->getLeaveRequestsOfCurrentAndPreviousFiscalYear($employee->id);
        $assignedAssets = $this->assetAssignLogs->with(['asset'])->where('assigned_user_id', $authUser->id)->get();
        $leaveEncashments = $this->employees->getLeaveEncashRequestsOfCurrentAndPreviousFiscalYear($employee->id);

        return view('Profile::show')
            ->withAssignedAssets($assignedAssets)
            ->withEmployee($employee)
            ->withLeaves($leaves)
            ->withLeaveRequests($leaveRequests)
            ->withPreviousLeaves($prevLeaves)
            ->withLeaveEncashments($leaveEncashments)
            ->withLeaveTypes($leaveTypes);
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $authUser = auth()->user();
        $employee = $this->employees->with([
            'medicalCondition', 'tenures.designation', 'tenures.department', 'tenures.supervisor',
            'tenures.crossSupervisor', 'tenures.nextLineManager', 'tenures.dutyStation',
            'trainings', 'address', 'experiences',
        ])->find($authUser->employee_id);
        $supervisors = $this->employees->select(['id', 'full_name', 'official_email_address'])
            ->where('id', '<>', $employee->id)
            ->whereNotNull('activated_at')
            ->get();

        return view('Profile::edit')
            ->withAuthUser(auth()->user())
            ->withBloodGroups($this->bloodGroups->get())
            ->withDepartments($this->departments->orderby('title', 'asc')->get())
            ->withDesignations($this->designations->orderby('title', 'asc')->get())
            ->withDistricts($this->districts->orderby('district_name', 'asc')->get())
            ->withEducation($this->education->get())
            ->withEducationLevels($this->educationLevel->get())
            ->withEmployee($employee)
            ->withFamilyRelations($this->familyRelations->orderby('title', 'asc')->get())
            ->withGenders($this->genders->get())
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withMaritalStatus($this->maritalStatus->get())
            ->withProvinces($this->provinces->get())
            ->withRoles($this->roles->orderby('role', 'asc')->get())
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Modules\Employee\Requests\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;

        if ($request->file('citizenship_attachment')) {
            $filename = $request->file('citizenship_attachment')
                ->storeAs($this->destinationPath.'/'.$employee->id, time().'_citizenship.'.$request->file('citizenship_attachment')->getClientOriginalExtension());
            $inputs['citizenship_attachment'] = $filename;
        }

        if ($request->file('pan_attachment')) {
            $filename = $request->file('pan_attachment')
                ->storeAs($this->destinationPath.'/'.$employee->id, time().'_pan.'.$request->file('pan_attachment')->getClientOriginalExtension());
            $inputs['pan_attachment'] = $filename;
        }
        $employee = $this->employees->update($employee->id, $inputs);
        if ($employee) {
            return redirect()->route('profile.edit', ['tab' => 'address'])
                ->withSuccessMessage('Profile successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Profile can not be updated.');
    }
}
