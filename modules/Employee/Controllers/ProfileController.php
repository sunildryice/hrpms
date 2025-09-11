<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;

use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\BloodGroupRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\EducationLevelRepository;
use Modules\Employee\Repositories\EducationRepository;
use Modules\Master\Repositories\FamilyRelationRepository;
use Modules\Master\Repositories\GenderRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\MaritalStatusRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Requests\StoreRequest;
use Modules\Employee\Requests\UpdateRequest;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Privilege\Repositories\RoleRepository;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param BloodGroupRepository $bloodGroups
     * @param DepartmentRepository $departments
     * @param DesignationRepository $designations
     * @param DistrictRepository $districts
     * @param EducationLevelRepository $educationLevel
     * @param EmployeeRepository $employees
     * @param FamilyRelationRepository $familyRelations
     * @param GenderRepository $genders
     * @param LocalLevelRepository $localLevels
     * @param MaritalStatusRepository $maritalStatus
     * @param OfficeRepository $offices
     * @param ProvinceRepository $provinces
     * @param RoleRepository $roles
     */
    public function __construct(
        BloodGroupRepository $bloodGroups,
        DepartmentRepository $departments,
        DesignationRepository $designations,
        DistrictRepository $districts,
        EducationRepository $education,
        EducationLevelRepository $educationLevel,
        EmployeeRepository $employees,
        FamilyRelationRepository $familyRelations,
        GenderRepository         $genders,
        LocalLevelRepository     $localLevels,
        MaritalStatusRepository  $maritalStatus,
        OfficeRepository         $offices,
        ProvinceRepository       $provinces,
        RoleRepository $roles
    )
    {
        $this->bloodGroups = $bloodGroups;
        $this->departments = $departments;
        $this->designations = $designations;
        $this->districts = $districts;
        $this->education = $education;
        $this->educationLevel = $educationLevel;
        $this->employees = $employees;
        $this->familyRelations = $familyRelations;
        $this->genders = $genders;
        $this->localLevels = $localLevels;
        $this->maritalStatus = $maritalStatus;
        $this->offices = $offices;
        $this->provinces = $provinces;
        $this->roles = $roles;
        $this->destinationPath = 'employees';
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $authUser = auth()->user();
        $employee = $this->employees->with([
            'medicalCondition', 'tenures.designation', 'tenures.department', 'tenures.supervisor',
            'tenures.crossSupervisor', 'tenures.nextLineManager', 'tenures.dutyStation',
            'trainings', 'address', 'experiences'
        ])->find($authUser->id);
        $supervisors = $this->employees->select(['id', 'full_name', 'official_email_address'])
            ->where('id', '<>', $employee->id)
            ->whereNotNull('activated_at')
            ->get();

        return view('Employee::Profile.edit')
            ->withAuthUser($authUser)
            ->withBloodGroups($this->bloodGroups->get())
            ->withDepartments($this->departments->get())
            ->withDesignations($this->designations->get())
            ->withDistricts($this->districts->get())
            ->withEducation($this->education->get())
            ->withEducationLevels($this->educationLevel->get())
            ->withDepartments($this->departments->orderby('title', 'asc')->get())
            ->withDesignations($this->designations->orderby('title', 'asc')->get())
            ->withDistricts($this->districts->orderby('district_name', 'asc')->get())
            ->withEmployee($employee)
            ->withFamilyRelations($this->familyRelations->orderby('position', 'asc')->get())
            ->withGenders($this->genders->get())
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withMaritalStatus($this->maritalStatus->get())
            ->withProvinces($this->provinces->get())
            ->withRoles($this->roles->get())
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param \Modules\Employee\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $employee = $this->employees->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : NULL;
        if($request->active && $employee->activated_at){
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
            return redirect()->route('employees.edit', [$employee->id, 'tab' => 'address'])
                ->withSuccessMessage('Employee successfully updated.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee can not be updated.');
    }

    public function profile($id)
    {
        $employee = $this->employees->find($id);
        return view('Employee::profile')
            ->withEmployee($employee);
    }
}
