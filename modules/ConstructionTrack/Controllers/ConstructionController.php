<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\ConstructionTrack\Repositories\ConstructionPartyRepository;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\ConstructionTrack\Requests\StoreRequest;
use Modules\ConstructionTrack\Requests\UpdateRequest;
use Modules\ConstructionTrack\Requests\UpdatewitProgressRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
// use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Privilege\Repositories\UserRepository;

class ConstructionController extends Controller
{
    private $districts;

    private $donors;

    private $provinces;

    private $employees;

    private $fiscalYears;

    private $constructions;

    private $localLevels;

    private $users;

    private $constructionParties;

    /**
     * Create a new controller instance.
     *
     * @param  ConstructionRepository  $advanceRequests
     */
    public function __construct(
        DistrictRepository $districts,
        DonorCodeRepository $donors,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        ConstructionRepository $constructions,
        LocalLevelRepository $localLevels,
        ProvinceRepository $provinces,
        UserRepository $users,
        OfficeRepository $offices,
        ConstructionPartyRepository $constructionParties
    ) {
        $this->districts = $districts;
        $this->donors = $donors;
        $this->provinces = $provinces;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->constructions = $constructions;
        $this->localLevels = $localLevels;
        $this->users = $users;
        $this->offices = $offices;
        $this->constructionParties = $constructionParties;
    }

    /**
     * Display a listing of the construction
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('construction');
        if ($request->ajax()) {
            $data = $this->constructions->with(['fiscalYear', 'status', 'latestAmendment'])->select(['*'])
                ->orderBy('effective_date_from', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('signed_date', function ($row) {
                    return $row->signed_date;
                })
                ->addColumn('effective_date_from', function ($row) {
                    return $row->effective_date_from;
                })
                ->addColumn('effective_date_to', function ($row) {
                    return $row->effective_date_to;
                })
                ->addColumn('extension_date_to', function ($row) {
                    return $row->latestAmendment ? $row->latestAmendment->getExtensionToDate() : '';
                })
                ->addColumn('physical_progress', function ($row) {
                    return $row->latestConstructionProgress->progress_percentage ? $row->latestConstructionProgress->progress_percentage.'%' : '';
                })
                ->addColumn('cluster', function ($row) {
                    return $row->getOfficeName();
                })
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })
                ->addColumn('locallevel', function ($row) {
                    return $row->getLocalName();
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('created_on', function ($row) {
                    return $row->created_at->toFormattedDateString();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('construction.show', $row->id).'" rel="tooltip" title="View Construction"><i class="bi bi-eye"></i></a>';
                    }
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('construction.edit', $row->id).'" rel="tooltip" title="Edit Construction"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete Construction" ';
                        $btn .= 'data-href="'.route('construction.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($authUser->can('addProgress', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('construction.edit.progress', $row->id).'" rel="tooltip" title="Manage Project Progress"><i class="bi bi-bar-chart"></i></a>';
                    }
                    if ($authUser->can('settlement', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('construction.settlement.edit', $row->id).'" rel="tooltip" title="Manage Settlement"><i class="bi bi-bank"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('ConstructionTrack::index');

    }

    /**
     * Show the form for creating a new Construction Track by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $this->authorize('manage-construction');
        $offices = $this->offices->getActiveOffices();

        return view('ConstructionTrack::create')
            ->with(['offices' => $offices])
            ->withDonors($this->donors->getEnabledDonorCodes())
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withDistricts($this->districts->orderby('district_name', 'asc')->get())
            ->withProvinces($this->provinces->get())
            ->withEmployees($this->employees->activeEmployees());
    }

    /**
     * Store a newly created advance request in storage.
     *
     * @param  \Modules\ConstructionExit\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        // $this->authorize('create-advance-request');
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $inputs['office_id'] = isset($inputs['office_id']) ? $inputs['office_id'] : $authUser->employee->office_id;
        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['updated_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        // $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if (! empty($inputs['approval'])) {
            $inputs['approval'] = 1;
        } else {
            $inputs['approval'] = 0;
        }
        // dd($inputs);
        $construction = $this->constructions->create($inputs);
        if ($construction) {
            $constructionParty = $this->constructionParties->create([
                'party_name' => 'OHW',
                'contribution_amount' => $request->ohw_contribution,
                'construction_id' => $construction->id,
                'deletable' => 0,
            ]);
            if ($constructionParty) {
                $this->constructionParties->updateContributionPercentage($construction->id);
            }

            return redirect()->route('construction.edit', $construction->id)
                ->withSuccessMessage('Construction is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Construction can not be added.');
    }

    public function updatewithprogress(UpdatewitProgressRequest $request) {}

    /**
     * Show the specified construction.
     *
     * @return mixed
     */
    public function show($constructionId)
    {
        $authUser = auth()->user();
        $constructions = $this->constructions->find($constructionId);
        $districts = $this->districts->get();

        return view('ConstructionTrack::show')
            ->withConstruction($constructions)
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withDistricts($districts)
            ->withDonors($this->donors->getEnabledDonorCodes())
            ->withAuthUser($authUser)
            ->withProvinces($this->provinces->get())
            ->withEmployees($this->employees->activeEmployees());
    }

    /**
     * Show the form for editing the specified construction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $this->authorize('update', $construction);
        $supervisors = $this->users->getSupervisors($authUser);
        $districts = $this->districts->get();
        $engineers = $this->employees->activeEmployees();
        $offices = $this->offices->getActiveOffices();
        if (! $construction->engineer?->activated_at) {
            $engineers->push($construction->engineer);
        }

        return view('ConstructionTrack::edit')
            ->withAuthUser($authUser)
            ->withDistricts($districts)
            ->withDonors($this->donors->getEnabledDonorCodes())
            ->withLocalLevels($this->localLevels->orderby('local_level_name', 'asc')->get())
            ->withProvinces($this->provinces->get())
            ->withConstruction($construction)
            ->withSupervisors($supervisors)
            ->withOffices($offices)
            ->withEngineers($engineers);
    }

    public function editProgress($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $supervisors = $this->users->getSupervisors($authUser);
        $districts = $this->districts->get();

        return view('ConstructionTrack::withprogress')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['office_id'] = isset($inputs['office_id']) ? $inputs['office_id'] : $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if (! empty($inputs['approval'])) {
            $inputs['approval'] = 1;
        } else {
            $inputs['approval'] = 0;
        }
        $construction = $this->constructions->update($id, $inputs);
        if ($construction) {
            $message = 'construction is successfully updated.';
            if ($construction->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Construction is successfully submitted.';

                return redirect()->route('construction.index')
                    ->withSuccessMessage($message);
            }

            return redirect()->route('construction.edit', $id)
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Construction can not be updated.');
    }

    /**
     * Remove the specified Construction request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $constructions = $this->constructions->find($id);
        $flag = $this->constructions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Construction is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Construction can not deleted.',
        ], 422);
    }
}
