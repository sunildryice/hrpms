<?php

namespace Modules\AdvanceRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\AdvanceRequest\Notifications\AdvanceRequestSubmitted;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Requests\StoreRequest;
use Modules\AdvanceRequest\Requests\UpdateRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

class AdvanceRequestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        DistrictRepository $districts,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        AdvanceRequestRepository $advanceRequests,
        ProjectCodeRepository $projects,
        UserRepository $users,
        OfficeRepository $offices
    ) {
        $this->districts = $districts;
        $this->projects = $projects;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->users = $users;
        $this->offices = $offices;
        $this->destinationPath = 'advanceRequest';
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->advanceRequests->with(['fiscalYear', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('required_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('advance_number', function ($row) {
                    return $row->getAdvanceRequestNumber();
                })->addColumn('project_code', function ($row) {
                    return $row->getProjectCode();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('required_date', function ($row) {
                    return $row->getRequiredDate();
                })->addColumn('estimated_amount', function ($row) {
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('advance.requests.edit', $row->id).'" rel="tooltip" title="Edit Advance Request"><i class="bi-pencil-square"></i></a>';
                    } elseif ($authUser->can('createSettlement', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('advance.settlement.create', $row->id).'" rel="tooltip" title="Create Settlement Request">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('advance.requests.destroy', $row->id).'" rel="tooltip" title="Delete Advance Request">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    } else {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('advance.requests.show', $row->id).'" rel="tooltip" title="View Advance Request"><i class="bi bi-eye"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('advance.request.print', $row->id).'" rel="tooltip" title="Print Advance Request"><i class="bi bi-printer"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::index');

    }

    /**
     * Show the form for creating a new advance request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $districts = $this->districts->getEnabledDistricts();
        $projects = $this->projects->getActiveProjectCodes();
        $offices = $this->offices->getActiveOffices();

        return view('AdvanceRequest::create')
            ->withDistricts($districts)
            ->withOffices($offices)
            ->withProjects($projects);
    }

    /**
     * Store a newly created advance request in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceRequest = $this->advanceRequests->create($inputs);
        if ($advanceRequest) {
            return redirect()->route('advance.requests.edit', $advanceRequest->id)
                ->withSuccessMessage('Advance Request successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Advance Request can not be added.');
    }

    /**
     * Show the specified advance request.
     *
     * @return mixed
     */
    public function show($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);

        return view('AdvanceRequest::show')
            ->withAdvanceRequest($advanceRequest);
    }

    /**
     * Show the form for editing the specified advance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('update', $advanceRequest);

        $approvers = $this->users->permissionBasedUsers('approve-advance-request');
        $verifiers = $this->users->permissionBasedUsers('verify-advance-request');
        $districts = $this->districts->getEnabledDistricts();
        $offices = $this->offices->getActiveOffices();
        $projects = $this->projects->getActiveProjectCodes();

        return view('AdvanceRequest::edit')
            ->withAuthUser(auth()->user())
            ->withApprovers($approvers)
            ->withDistricts($districts)
            ->withOffices($offices)
            ->withProjects($projects)
            ->withAdvanceRequest($advanceRequest)
            ->withVerifiers($verifiers);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('update', $advanceRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceRequest = $this->advanceRequests->update($id, $inputs);
        if ($advanceRequest) {
            $message = 'Advance request is successfully updated.';
            if ($advanceRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Advance request is successfully submitted.';
                $advanceRequest->verifier->notify(new AdvanceRequestSubmitted($advanceRequest));
                return redirect()->route('advance.requests.index')
                    ->withSuccessMessage($message);
            }
            return redirect()->back()->withInput()
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Advance Request can not be updated.');
    }

    /**
     * Remove the specified advance request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
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
