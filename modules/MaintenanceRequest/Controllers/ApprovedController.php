<?php

namespace Modules\MaintenanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use DB;
use DataTables;

class ApprovedController extends Controller
{
    private $destinationPath;
    private $employees;
    private $maintenanceRequest;
    private $roles;
    private $status;
    private $user;

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
            $data = $this->maintenanceRequest->getApproved();

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
                    if ($authUser->can('show', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.maintenance.requests.show', $row->id) . '" rel="tooltip" title="View Approved Maintenance Request"><i class="bi bi-eye"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('approved.maintenance.requests.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('MaintenanceRequest::Approved.index');
    }

    /**
     * Veiw the specified maintenance request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function show($id)
    {
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('show', $maintenanceRequest);
        return view('MaintenanceRequest::Approved.view')
            ->withMaintenanceRequest($maintenanceRequest);
    }

    /**
     * Show the specified fund request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('print', $maintenanceRequest);

         $requester = $maintenanceRequest->requester?->employee;
        $reviewer = $maintenanceRequest->reviewer?->employee;
        $approver = $maintenanceRequest->approver?->employee;

        $requesterSignature = null;
        if ($requester && $requester->signature && file_exists(public_path('storage/' . $requester->signature))) {
            $requesterSignature = asset('storage/' . $requester->signature);
        }

        $reviewerSignature = null;
        if ($reviewer && $reviewer->signature && file_exists(public_path('storage/' . $reviewer->signature))) {
            $reviewerSignature = asset('storage/' . $reviewer->signature);
        }

        $approverSignature = null;
        if ($approver && $approver->signature && file_exists(public_path('storage/' . $approver->signature))) {
            $approverSignature = asset('storage/' . $approver->signature);
        }

        return view('MaintenanceRequest::print')
            ->withMaintenanceRequest($maintenanceRequest)
            ->withRequester($maintenanceRequest->requester->employee)
            ->withRequesterSignature($requesterSignature)
            ->withReviewerSignature($reviewerSignature)
            ->withApproverSignature($approverSignature);
    }
}
