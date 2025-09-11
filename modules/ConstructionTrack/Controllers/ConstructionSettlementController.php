<?php

namespace Modules\ConstructionTrack\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\ConstructionTrack\Requests\StoreRequest;
use Modules\ConstructionTrack\Requests\UpdateRequest;
use Modules\ConstructionTrack\Requests\UpdatewitProgressRequest;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProvinceRepository;
// use Modules\Employee\Repositories\EmployeeRepository;
use Modules\ConstructionTrack\Notifications\ConstructionSubmitted;
use DataTables;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DonorCodeRepository;

class ConstructionSettlementController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param ConstructionRepository $advanceRequests
     * @param ProvinceRepository $provinces
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository       $districts,
        DonorCodeRepository      $donors,
        EmployeeRepository       $employees,
        FiscalYearRepository     $fiscalYears,
        ConstructionRepository   $constructions,
        ProvinceRepository       $provinces,
        UserRepository           $users
    )
    {
        $this->districts = $districts;
        $this->donors = $donors;
        $this->provinces = $provinces;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->constructions = $constructions;
        $this->users = $users;
        // $this->destinationPath = 'advanceRequest';
    }

  



    /**
     * Show the form for editing the specified construction.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        // $this->authorize('update', $construction);

        $supervisors = $this->users->getSupervisors($authUser);

        $districts = $this->districts->get();

        return view('ConstructionTrack::Settlement.edit')
            ->withAuthUser(auth()->user())
            ->withDistricts($districts)
            ->withDonors($this->donors->getEnabledDonorCodes())
            ->withProvinces($this->provinces->get())
            ->withConstruction($construction)
            ->withSupervisors($supervisors)
            ->withEmployees($this->employees->activeEmployees());
        
    }


  
    /**
     * Update the specified construction is updated
     *
     * @param \Modules\ConstructionTrack\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $construction = $this->constructions->find($id);
        // $this->authorize('update', $advanceRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        // $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $construction = $this->constructions->update($id, $inputs);
        if ($construction) {
            $message = 'construction is successfully updated.';
            return redirect()->route('construction.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Construction can not be updated.');
    }

    /**
     * Remove the specified advance request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('delete', $advanceRequest);
        $flag = $this->advanceRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Advance request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Advance request can not deleted.',
        ], 422);
    }

}
