<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\FamilyDetailRepository;
use Modules\Employee\Requests\FamilyDetail\StoreRequest;
use Modules\Employee\Requests\FamilyDetail\UpdateRequest;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FamilyRelationRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\ProvinceRepository;

class FamilyDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  DistrictRepository $districts
     * @param  EmployeeRepository $employees
     * @param  FamilyDetailRepository $familyDetails
     * @param  FamilyRelationRepository $familyRelations
     * @param  LocalLevelRepository $localLevels
     * @param  ProvinceRepository $provinces
     * @return void
     */
    public function __construct(
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected FamilyDetailRepository $familyDetails,
        protected FamilyRelationRepository $familyRelations,
        protected LocalLevelRepository $localLevels,
        protected ProvinceRepository $provinces
    )
    {
    }

    /**
     * Store a newly created employee family member in storage.
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
        $inputs['created_by'] = auth()->id();
        $inputs['emergency_contact_at'] = $request->emergency_contact ? date('Y-m-d H:i:s'): NULL;
        $inputs['nominee_at'] = $request->nominee ? date('Y-m-d H:i:s'): NULL;
        $family = $this->familyDetails->create($inputs);
        if($family){
            return redirect()->route('profile.edit', ['tab'=>'family-details'])
                ->withSuccessMessage('Family detail is successfully added.');
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Family detail can not be added.');
    }

    /**
     * Show the form for editing the specified employee family member.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
        $authUser = auth()->user();
        $familyMember = $this->familyDetails->with(['employee'])->find($id);
        if($request->wantsJson()){
            return response()->json([
                'familyMember'=>$familyMember,
                'dateOfBirth'=>$familyMember->date_of_birth ? $familyMember->date_of_birth->format('Y-m-d') : '',
                'updateAction'=>route('profile.family.details.update', [$familyMember->id]),
            ]);
        }

        return view('Employee::FamilyDetail.edit')
            ->withEmployee($authUser->employee)
            ->withFamilyMember($familyMember)
            ->withFamilyRelations($this->familyRelations->get())
            ->withDistricts($this->districts->where('province_id', '=', $familyMember->province_id)->get())
            ->withProvinces($this->provinces->get())
            ->withLocalLevels($this->localLevels->where('district_id', '=', $familyMember->district_id)->get());
    }

    /**
     * Update the specified employee family member in storage.
     *
     * @param \Modules\Employee\Requests\MedicalCondition\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $familyDetail = $this->familyDetails->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['emergency_contact_at'] = $request->emergency_contact ? date('Y-m-d H:i:s'): NULL;
        $inputs['nominee_at'] = $request->nominee ? date('Y-m-d H:i:s'): NULL;
        if($request->nominee && $familyDetail->nominee_at){
            unset($inputs['nominee_at']);
        }
        $familyDetail = $this->familyDetails->update($id, $inputs);

        if($familyDetail){
            return redirect()->route('profile.edit', ['tab'=>'family-details'])
                ->withSuccessMessage('Family detail is successfully updated.');
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Family detail can not be updated.');
    }
}
