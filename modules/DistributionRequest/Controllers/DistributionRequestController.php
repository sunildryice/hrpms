<?php

namespace Modules\DistributionRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\DistributionRequest\Notifications\DistributionRequestSubmitted;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\DistributionRequest\Requests\StoreRequest;
use Modules\DistributionRequest\Requests\UpdateRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\HealthFacilityRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;

class DistributionRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistributionRequestRepository $distributionRequests
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param OfficeRepository $offices
     * @param ProjectCodeRepository $projectCodes
     * @param UserRepository $users
     */
    public function __construct(
        DistributionRequestRepository $distributionRequests,
        DistrictRepository            $districts,
        EmployeeRepository            $employees,
        FiscalYearRepository          $fiscalYears,
        HealthFacilityRepository      $healthFacilities,
        InventoryItemRepository       $inventoryItems,
        ItemRepository                $items,
        OfficeRepository              $offices,
        ProjectCodeRepository         $projectCodes,
        UserRepository                $users,
        PurchaseRequestRepository     $purchaseRequests,
    )
    {
        $this->distributionRequests = $distributionRequests;
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->healthFacilities = $healthFacilities;
        $this->inventoryItems = $inventoryItems;
        $this->items = $items;
        $this->offices = $offices;
        $this->projectCodes = $projectCodes;
        $this->users = $users;
        $this->purchaseRequests = $purchaseRequests;
        $this->destinationPath = 'distributionRequest';
    }

    /**
     * Display a listing of the distribution requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->distributionRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])
                ->select(['*'])
                ->whereCreatedBy($authUser->id);

            return DataTables::of($data)
                ->addIndexColumn()
                ->filterColumn('district', function($query, $keyword) {
                    $query->whereHas('district', function($q) use ($keyword) {
                        $q->where('district_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('health_facility', function($query, $keyword) {
                    $query->whereHas('healthFacility', function($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })->addColumn('project', function ($row) {
                    return $row->getProjectCode();
                })->addColumn('requisition_number', function ($row) {
                    return $row->getDistributionRequestNumber();
                })->addColumn('health_facility', function ($row) {
                    return $row->getHealthFacility();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('distribution.requests.show', $row->id) . '" rel="tooltip" title="View Distribution Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('distribution.requests.edit', $row->id) . '" rel="tooltip" title="Edit Distribution Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('distribution.requests.destroy', $row->id) . '" rel="tooltip" title="Delete Distribution Request">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::index');
    }

    /**
     * Show the form for creating a new distribution request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $districts = $this->districts->getEnabledDistricts();
        $offices = $this->offices->getActiveOffices();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $healthFacilities = $this->healthFacilities->getHealthFacilities();
        $purchaseRequests = $this->purchaseRequests->getApproved();
        return view('DistributionRequest::create')
            ->withDistricts($districts)
            ->withOffices($offices)
            ->withHealthFacilities($healthFacilities)
            ->withProjectCodes($projectCodes)
            ->withPurchaseRequests($purchaseRequests);
    }

    /**
     * Store a newly created distribution request in storage.
     *
     * @param \Modules\DistributionRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionRequest = $this->distributionRequests->create($inputs);

        if ($distributionRequest) {
            return redirect()->route('distribution.requests.edit', $distributionRequest->id)
                ->withSuccessMessage('Distribution Request successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Distribution Request can not be added.');
    }

    /**
     * Show the specified distribution request.
     *
     * @param $distributionRequestId
     * @return mixed
     */
    public function show($distributionRequestId)
    {
        $authUser = auth()->user();
        $distributionRequest = $this->distributionRequests->find($distributionRequestId);

        return view('DistributionRequest::show')
            ->withDistributionRequest($distributionRequest);
    }

    /**
     * Show the form for editing the specified distribution request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $distributionRequest = $this->distributionRequests->find($id);
        $this->authorize('update', $distributionRequest);

        $supervisors = $this->users->getSupervisors($authUser);
        $districts = $this->districts->getEnabledDistricts();
        $offices = $this->offices->getActiveOffices();
        $healthFacilities = $this->healthFacilities->getHealthFacilities();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();

        return view('DistributionRequest::edit')
            ->withAuthUser($authUser)
            ->withDistricts($districts)
            ->withDistributionRequest($distributionRequest)
            ->withOffices($offices)
            ->withHealthFacilities($healthFacilities)
            ->withProjectCodes($projectCodes)
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified distribution request in storage.
     *
     * @param \Modules\DistributionRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $distributionRequest = $this->distributionRequests->find($id);
        $this->authorize('update', $distributionRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionRequest = $this->distributionRequests->update($id, $inputs);

        if ($distributionRequest) {
            $message = 'Distribution request is successfully updated.';
            if ($distributionRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Distribution request is successfully submitted.';
                $distributionRequest->approver->notify(new DistributionRequestSubmitted($distributionRequest));
            }
            return redirect()->route('distribution.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Distribution Request can not be updated.');
    }

    /**
     * Remove the specified distribution request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $distributionRequest = $this->distributionRequests->find($id);
        $this->authorize('delete', $distributionRequest);
        $flag = $this->distributionRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Distribution request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Distribution request can not deleted.',
        ], 422);
    }

    /**
     * Amend the specified distribution request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $distributionRequest = $this->distributionRequests->find($id);
        $this->authorize('amend', $distributionRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->distributionRequests->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Distribution request is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Distribution request can not amended.',
        ], 422);
    }
}
