<?php

namespace Modules\VehicleRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\VehicleRequest\Notifications\VehicleRequestApproved;
use Modules\VehicleRequest\Notifications\VehicleRequestApprovedProcurement;
use Modules\VehicleRequest\Notifications\VehicleRequestRejected;
use Modules\VehicleRequest\Notifications\VehicleRequestReturned;
use Modules\VehicleRequest\Notifications\VehicleRequestSubmitted;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\VehicleRequest\Requests\Approve\StoreRequest;
use DataTables;


class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param VehicleRequestRepository $vehicleRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        VehicleRequestRepository $vehicleRequests,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->vehicleRequests = $vehicleRequests;
        $this->users = $users;
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
            $data = $this->vehicleRequests->with(['status', 'vehicleRequestType', 'requester'])->select(['*'])
                ->where('vehicle_request_type_id', 2)
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->get();

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
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.vehicle.requests.create', $row->id) . '" rel="tooltip" title="Approve Vehicle Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('VehicleRequest::Approve.index');
    }

    public function create($vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        if ($vehicleRequest->vehicle_request_type_id == 1) {
            return redirect()->route('assign.vehicle.requests.create', $vehicleRequest->id);
        }
        $this->authorize('approve', $vehicleRequest);

        $approvers =  $vehicleRequest->vehicle_request_type_id == 1 ? collect() : $this->users->permissionBasedUsers('approve-hire-vehicle-request');

        return view('VehicleRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withVehicleRequest($vehicleRequest)
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $vehicleRequestId)
    {
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('approve', $vehicleRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $vehicleRequest = $this->vehicleRequests->approve($vehicleRequest->id, $inputs);
        $officers = $vehicleRequest->procurementOfficers;

        if ($vehicleRequest) {
            $message = '';
            if ($vehicleRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Vehicle request is successfully returned.';
                $vehicleRequest->requester->notify(new VehicleRequestReturned($vehicleRequest));
            } else if ($vehicleRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Vehicle request is successfully rejected.';
                $vehicleRequest->requester->notify(new VehicleRequestRejected($vehicleRequest));
            } else if ($vehicleRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Vehicle request is successfully recommended.';
                $vehicleRequest->approver->notify(new VehicleRequestSubmitted($vehicleRequest));
            } else {
                $message = 'Vehicle request is successfully approved.';
                $vehicleRequest->requester->notify(new VehicleRequestApproved($vehicleRequest));
                foreach($officers as $officer){
                    $officer->notify(new VehicleRequestApprovedProcurement($vehicleRequest));
                }
            }

            return redirect()->route('approve.vehicle.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Vehicle request can not be approved.');
    }
}
