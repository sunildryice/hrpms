<?php

namespace Modules\MaintenanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestSubmittedApprove;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestReviewd;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestForwarded;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestForRecommend;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestRecommended;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestRejected;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestReturned;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\MaintenanceRequest\Requests\MaintenanceRequestReview\StoreRequest;
use DB;
use DataTables;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param MaintenanceRequestRepository $maintenanceRequest
     * @param RoleRepository $roles
     * @param StatusRepository $status
     * @param UserRepository $user
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

        if ($request->ajax()) {
            $data = $this->maintenanceRequest->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
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
                    if ($authUser->can('review', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('review.maintenance.requests.create', $row->id) . '" rel="tooltip" title="Review Maintenance Request"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('MaintenanceRequest::Review.index');
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
        $this->authorize('review', $maintenanceRequest);

        return view('MaintenanceRequest::Review.create')
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
        $this->authorize('review', $maintenanceRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $maintenanceRequest = $this->maintenanceRequest->review($maintenanceRequest->id, $inputs);

        if ($maintenanceRequest) {
            $message = '';
            if ($maintenanceRequest->status_id == 2) {
                $message = 'Maintenance request is successfully returned.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestReturned($maintenanceRequest));
            } else if ($maintenanceRequest->status_id == 8) {
                $message = 'Maintenance request is successfully rejected.';
                $maintenanceRequest->requester->notify(new MaintenanceRequestRejected($maintenanceRequest));
            } else {
                $message = 'Maintenance request is successfully reviewed.';
                $maintenanceRequest->approver->notify(new MaintenanceRequestSubmittedApprove($maintenanceRequest));
            }

            return redirect()->route('review.maintenance.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Maintenance request can not be reviewd.');
    }
}
