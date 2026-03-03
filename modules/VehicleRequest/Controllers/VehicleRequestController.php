<?php

namespace Modules\VehicleRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;

use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\VehicleRequestTypeRepository;

use Modules\Master\Repositories\VehicleTypeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\Project\Repositories\ProjectRepository;
use Modules\VehicleRequest\Notifications\VehicleRequestSubmitted;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\VehicleRequest\Requests\StoreRequest;
use Modules\VehicleRequest\Requests\UpdateRequest;

class VehicleRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param AccountCodeRepository $accountCodes
     * @param DistrictRepository $districts
     * @param DonorCodeRepository $donorCodes
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param OfficeRepository $offices
     * @param VehicleRequestTypeRepository $vehicleRequestTypes
     * @param VehicleRequestRepository $vehicleRequests
     * @param VehicleTypeRepository $vehicleTypes
     * @param UserRepository $users
     * @param ProjectCodeRepository $projectCodes
     */
    public function __construct(
        ActivityCodeRepository $activityCodes,
        AccountCodeRepository $accountCodes,
        DistrictRepository $districts,
        DonorCodeRepository $donorCodes,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        OfficeRepository $offices,
        VehicleRequestTypeRepository $vehicleRequestTypes,
        VehicleRequestRepository $vehicleRequests,
        VehicleTypeRepository $vehicleTypes,
        UserRepository $users,
        ProjectCodeRepository $projectCodes,
        ProjectRepository $projects,
    ) {
        $this->activityCodes = $activityCodes;
        $this->accountCodes = $accountCodes;
        $this->districts = $districts;
        $this->donorCodes = $donorCodes;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->offices = $offices;
        $this->vehicleRequestTypes = $vehicleRequestTypes;
        $this->vehicleRequests = $vehicleRequests;
        $this->vehicleTypes = $vehicleTypes;
        $this->users = $users;
        $this->projectCodes = $projectCodes;
        $this->projects = $projects;
        $this->destinationPath = 'vehicleRequest';
    }

    /**
     * Display a listing of the vehicle requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->vehicleRequests->with(['vehicleRequestType', 'requester', 'status', 'logs'])
                ->whereRequesterId($authUser->id)
                // ->orWhereHas('logs', function ($q) use ($authUser) {
                //     $q->where('user_id', $authUser->id);
                //     $q->orWhere('original_user_id', $authUser->id);
                // })
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getVehicleRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('start_datetime', function ($row) {
                    return $row->getStartDatetime();
                })->addColumn('end_datetime', function ($row) {
                    return $row->getEndDatetime();
                })->addColumn('vehicle_request_type', function ($row) {
                    return $row->getVehicleRequestType();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('vehicle.requests.edit', $row->id) . '" rel="tooltip" title="Edit Vehicle Request"><i class="bi-pencil-square"></i></a>';
                    } else {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('vehicle.requests.show', $row->id) . '" rel="tooltip" title="Show Vehicle Request"><i class="bi-eye"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('vehicle.requests.print', $row->id) . '" rel="tooltip" title="Print Vehicle Request"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('vehicle.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    } else if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-vehicle-request"';
                        $btn .= 'data-href = "' . route('vehicle.requests.amend.store', $row->id) . '" title="Amend Vehicle Request">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('VehicleRequest::index');
    }

    /**
     * Show the form for creating a new vehicle request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $vehicleRequestTypes = $this->vehicleRequestTypes->get();
        $vehicleTypes = $this->vehicleTypes->get();
        $activeStaffs = $this->employees->getActiveEmployees();
        $employees = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $districts = $this->districts->getDistricts();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $approvers = $this->users->permissionBasedUsers('assign-office-vehicle');
        $approvers = $approvers->reject(function ($approver) use ($authUser) {
            return $approver->id == $authUser->id;
        });

        $hireApprovers = $this->users->permissionBasedUsers('approve-hire-vehicle-request');
        $officers = $this->users->permissionBasedUsers('manage-hire-vehicle-procurement');
        // $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $projects = $this->projects->getAssignedProjects($authUser);

        return view('VehicleRequest::create')
            ->withActivityCodes($activityCodes)
            ->withProjects($projects)
            ->withApprovers($approvers)
            ->withDistricts($districts)
            ->withDonorCodes($donorCodes)
            ->withEmployees($employees)
            ->withHireApprovers($hireApprovers)
            ->withOffices($this->offices->select(['*'])->whereNotNull('activated_at')->get())
            ->withVehicleRequestTypes($vehicleRequestTypes)
            ->withOfficers($officers)
            ->withVehicleTypes($vehicleTypes);
    }

    /**
     * Store a newly created vehicle request in storage.
     *
     * @param \Modules\VehicleRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {

        $authUser = auth()->user();
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $vehicleType = 'hire-vehicle';
        if ($inputs['vehicle_request_type_id'] == 1) {
            $inputs['start_datetime'] = $inputs['office_start_datetime'];
            $inputs['end_datetime'] = $inputs['office_end_datetime'];
        }
        $inputs['employee_ids'] = json_encode($request->employee_ids ? $request->employee_ids : []);
        $inputs['district_ids'] = json_encode($request->district_ids ? $request->district_ids : []);
        $inputs['vehicle_type_ids'] = $request->vehicle_type_ids ? json_encode($request->vehicle_type_ids) : json_encode([$request->vehicle_type_id]);
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $vehicleRequest = $this->vehicleRequests->create($inputs);

        if ($vehicleRequest) {
            $message = 'Vehicle request is successfully added.';
            if ($vehicleRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Vehicle request is successfully submitted.';
                $vehicleRequest->approver->notify(new VehicleRequestSubmitted($vehicleRequest));
            }
            return redirect()->route('vehicle.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Vehicle Request can not be added.');
    }

    /**
     * Show the form for editing the specified vehicle request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($id);
        $this->authorize('update', $vehicleRequest);
        $vehicleTypes = $this->vehicleTypes->get();
        $activeStaffs = $this->employees->getActiveEmployees();
        $employees = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $accountCodes = $vehicleRequest->activityCode ? $vehicleRequest->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $districts = $this->districts->getDistricts();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $approvers = $this->users->permissionBasedUsers('assign-office-vehicle');
        $approvers = $approvers->reject(function ($approver) use ($authUser) {
            return $approver->id == $authUser->id;
        });
        $hireApprovers = $this->users->permissionBasedUsers('approve-hire-vehicle-request');
        $officers = $this->users->permissionBasedUsers('manage-hire-vehicle-procurement');
        // $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $projects = $this->projects->getAssignedProjects($authUser);

        $view = $vehicleRequest->vehicle_request_type_id == 1 ? view('VehicleRequest::editoffice') : view('VehicleRequest::edithire');
        return $view->withActivityCodes($activityCodes)
            ->withAccountCodes($accountCodes)
            ->withProjects($projects)
            ->withApprovers($approvers)
            ->withDistricts($districts)
            ->withDonorCodes($donorCodes)
            ->withEmployees($employees)
            ->withHireApprovers($hireApprovers)
            ->withOffices($this->offices->select(['*'])->whereNotNull('activated_at')->get())
            ->withVehicleRequest($vehicleRequest)
            ->withOfficers($officers)
            ->withVehicleTypes($vehicleTypes);
    }

    /**
     * Update the specified vehicle request in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $vehicleRequest = $this->vehicleRequests->find($id);
        $this->authorize('update', $vehicleRequest);
        $inputs = $request->validated();
        if ($vehicleRequest->vehicle_request_type_id == 1) {
            $inputs['start_datetime'] = $inputs['office_start_datetime'];
            $inputs['end_datetime'] = $inputs['office_end_datetime'];
            $vehicleType = 'office-vehicle';
        }
        $inputs['employee_ids'] = json_encode($request->employee_ids ? $request->employee_ids : []);
        $inputs['district_ids'] = json_encode($request->district_ids ? $request->district_ids : []);
        $inputs['vehicle_type_ids'] = $request->vehicle_type_ids ? json_encode($request->vehicle_type_ids) : json_encode([$request->vehicle_type_id]);
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $vehicleRequest = $this->vehicleRequests->update($id, $inputs);
        if ($vehicleRequest) {
            $message = 'Vehicle request is successfully updated.';
            if ($vehicleRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Vehicle request is successfully submitted.';
                $vehicleRequest->approver->notify(new VehicleRequestSubmitted($vehicleRequest));
            }
            return redirect()->route('vehicle.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Vehicle Request can not be updated.');
    }

    /**
     * Remove the specified vehicle request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $vehicleRequest = $this->vehicleRequests->find($id);
        $this->authorize('delete', $vehicleRequest);
        $flag = $this->vehicleRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Vehicle request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Vehicle request can not deleted.',
        ], 422);
    }

    /**
     * Show the specified vehicle request.
     *
     * @param $vehicleRequestId
     * @return mixed
     */
    public function show($vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);

        return view('VehicleRequest::show')
            ->withVehicleRequest($vehicleRequest);
    }

    public function print($vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);

        return view('VehicleRequest::print')
            ->withVehicleRequest($vehicleRequest);
    }

    /**
     * Amend the specified vehicle request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $vehicleRequest = $this->vehicleRequests->find($id);
        $this->authorize('amend', $vehicleRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->vehicleRequests->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Vehicle request is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Vehicle request can not amended.',
        ], 422);
    }

    /**
     * Show the specified vehicle request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printRequest($id)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($id);
        //        $this->authorize('print', $vehicleRequest);

        return view('VehicleRequest::print')
            ->withVehicleRequest($vehicleRequest);
    }
}
