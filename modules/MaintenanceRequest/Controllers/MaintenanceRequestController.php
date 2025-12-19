<?php

namespace Modules\MaintenanceRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestSubmitted;
use Modules\MaintenanceRequest\Notifications\MaintenanceRequestSubmittedApprove;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\MaintenanceRequest\Requests\StoreRequest;
use Modules\MaintenanceRequest\Requests\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;

class MaintenanceRequestController extends Controller
{
    protected $maintenanceRequest;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected UserRepository $users,
        MaintenanceRequestRepository $maintenanceRequest,
    ) {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    /**
     * Display a listing of the Maintenance and Repair request by user id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();

        if ($request->ajax()) {
            $data = $this->maintenanceRequest
                ->select(['*'])
                ->with([
                    'status',
                    'fiscalYear',
                    'requester',
                ])
                ->where(function ($q) use ($userId) {
                    $q->where('created_by', $userId);
                })
                ->orWhere(function ($q) use ($userId) {
                    $q->where('requester_id', $userId);
                })
                ->orWhere(function ($q) {
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.VERIFIED_STATUS')]);
                })
                ->orderBy('created_at', 'desc')
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
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('maintenance.requests.view', encrypt($row->id)) . '" rel="tooltip" title="View Maintenance Request"><i class="bi-eye-fill"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('maintenance.requests.edit', $row->id) . '" rel="tooltip" title="Edit Maintenance Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('approved.maintenance.requests.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('maintenance.requests.destroy', $row->id) . '" rel="tooltip" title="Delete Maintenance Request">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    } elseif ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-danger amend-record"';
                        $btn .= 'data-href = "' . route('maintenance.requests.amend', $row->id) . '" data-number="' . $row->getMaintenanceRequestNumber() . '" title="Reverse Maintenance Requset">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('MaintenanceRequest::index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $reviewers = $this->users->getSupervisors($authUser);

        return view('MaintenanceRequest::create')
            ->withReviewers($reviewers);
    }

    /**
     * Store a newly created maintenance request in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['user_id'] = $inputs['created_by'] = $inputs['requester_id'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['status_id'] = config('constant.CREATED_STATUS');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $maintenanceRequest = $this->maintenanceRequest->create($inputs);

        if ($maintenanceRequest) {
            $message = 'Maintenance request is successfully added.';

            return response()->json([
                'status' => 'ok',
                'maintenanceRequest' => $maintenanceRequest,
                'route' => route('maintenance.requests.edit', $maintenanceRequest->id),
                'message' => $message
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Maintenance Request can not be added.'
        ], 422);
    }

    /**
     * Show the form for editing the specified maintenance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('update', $maintenanceRequest);
        $reviewers = $this->users->permissionBasedUsers('review-maintenance-request');
        $approvers = $this->users->permissionBasedUsers('approve-maintenance-request');

        return view('MaintenanceRequest::edit')
            ->withAuthUser($authUser)
            ->withApprovers($approvers)
            ->withReviewers($reviewers)
            ->withMaintenanceRequest($maintenanceRequest);
    }

    /**
     * Update the specified employee in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('update', $maintenanceRequest);
        $inputs = $request->validated();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['updated_by'] = auth()->id();
        $maintenanceRequest = $this->maintenanceRequest->update($id, $inputs);
        if ($maintenanceRequest) {
            $message = 'Maintenance request is successfully updated.';

            if ($maintenanceRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Maintenance request is successfully updated and submitted.';
                $maintenanceRequest->reviewer->notify(new MaintenanceRequestSubmitted($maintenanceRequest));

                return redirect()->route('maintenance.requests.index')
                    ->withSuccessMessage($message);
            } elseif ($maintenanceRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Maintenance request is successfully submitted for approval.';
                $maintenanceRequest->approver->notify(new MaintenanceRequestSubmittedApprove($maintenanceRequest));

                return redirect()->route('maintenance.requests.index')
                    ->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Maintenance Request can not be updated.');
    }

    /**
     * Veiw the specified maintenance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($payload)
    {
        try {
            $id = decrypt($payload);
            $maintenanceRequest = $this->maintenanceRequest->find($id);

            return view('MaintenanceRequest::view')
                ->withMaintenanceRequest($maintenanceRequest);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $exception) {
            return view('errors.404');
        }
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $maintenanceRequest = $this->maintenanceRequest->find($id);
        $this->authorize('delete', $maintenanceRequest);
        $flag = $this->maintenanceRequest->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Maintenance and Repair Request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Maintenance and Repair Request can not deleted.',
        ], 422);
    }

    public function amend(Request $request, $id)
    {
        $maintenance = $this->maintenanceRequest->find($id);
        $this->authorize('amend', $maintenance);
        $inputs = $request->validate(['modification_remarks' => 'required|string']);
        $inputs['created_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $clone = $this->maintenanceRequest->amend($id, $inputs);
        if ($clone) {
            return response()->json([
                'type' => 'success',
                'message' => 'Maintenance Request is successfully amended.',
                'redirectUrl' => route('maintenance.requests.edit', $clone->id),
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Maintenance Request can not amended.',
        ], 422);
    }
}
