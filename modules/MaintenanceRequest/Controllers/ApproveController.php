<?php

namespace Modules\MaintenanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestApproved;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestForwarded;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestRecommended;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestRejected;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestReturned;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\MaintenanceRequest\Requests\MaintenanceRequestReview\StoreRequest;
use DB;
use DataTables;

class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository $employees ,
     * @param MaintenanceRequestRepository $maintenanceRequest ,
     * @param RoleRepository $roles ,
     * @param StatusRepository $status ,
     * @param UserRepository $user
     *
     */

    public function __construct(
        EmployeeRepository           $employees,
        MaintenanceRequestRepository $maintenanceRequest,
        RoleRepository               $roles,
        StatusRepository             $status,
        UserRepository               $user
    )
    {
        $this->employees = $employees;
        $this->maintenanceRequest = $maintenanceRequest;
        $this->roles = $roles;
        $this->status = $status;
        $this->user = $user;
        $this->destinationPath = 'MaintenanceRequest';
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();

        if ($request->ajax()) {
            $data = $this->maintenanceRequest->select(['*'])
                ->where(function ($q) use ($userId) {
                    $q->where('approver_id', $userId);
                    $q->whereIn('status_id', [4,11]);
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('maintenance_number', function ($row) {
                    return $row->getMaintenanceRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('approve', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approve.maintenance.requests.create', $row->id) . '" rel="tooltip" title="Approve Maintenance Request"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('MaintenanceRequest::Approve.index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('approve', $maintenanceRequest);

        $logisticOfficers = $this->user->permissionBasedUsers('view-approved-maintenance-request');

        return view('MaintenanceRequest::Approve.create')
            ->withLogisticOfficers($logisticOfficers)
            ->withMaintenanceRequest($maintenanceRequest);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $userId = auth()->id();
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('approve', $maintenanceRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $maintenanceRequest = $this->maintenanceRequest->approve($maintenanceRequest->id, $inputs);

        if ($maintenanceRequest) {
            $message = '';
            if ($maintenanceRequest->status_id == 2) {
                $message = 'Maintenance request is successfully returned.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestReturned($maintenanceRequest));
            } else if ($maintenanceRequest->status_id == 4) {
                $message = 'Maintenance request is successfully recommended.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestRecommended($maintenanceRequest));
            } else if ($maintenanceRequest->status_id == 8) {
                $message = 'Maintenance request is successfully rejected.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestRejected($maintenanceRequest));
            } else {
                $message = 'Maintenance request is successfully approved.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestApproved($maintenanceRequest));
                $maintenanceRequest->logisticOfficer->notify(new MaintenanceRequestForwarded($maintenanceRequest));
            }

            return redirect()->route('approve.maintenance.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Maintenance request can not be approved.');
    }
}
