<?php

namespace Modules\AdvanceRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementSubmitted;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\AdvanceRequest\Requests\Settlement\StoreRequest;
use Modules\AdvanceRequest\Requests\Settlement\UpdateRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

class SettlementController extends Controller
{
    private $districts;
    private $projects;
    private $employees;
    private $fiscalYears;
    private $advanceRequests;
    private $settlements;
    private $users;
    private $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository $districts,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        AdvanceRequestRepository $advanceRequests,
        SettlementRepository $settlements,
        ProjectCodeRepository $projects,
        UserRepository $users
    ) {
        $this->districts = $districts;
        $this->projects = $projects;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->settlements = $settlements;
        $this->users = $users;
        $this->destinationPath = 'advanceRequest';
    }

    /**
     * Display a listing of the settlement requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        // $this->authorize('advance-settlement');

        if ($request->ajax()) {
            $data = $this->settlements->with(['status', 'logs'])
                ->where(function ($q) use ($authUser) {
                    $q->where('created_by', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
                $q->orWhere('original_user_id', $authUser->id);
            })->orderBy('completion_date', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('advance_number', function ($row) {
                return $row->advanceRequest->getAdvanceRequestNumber();
            })->addColumn('request-date', function ($row) {
                return $row->advanceRequest->getRequestDate();
            })->addColumn('expense_amount', function ($row) {
                return $row->getSettlementExpenseAmount();
            })->addColumn('completion_date', function ($row) {
                return $row->getCompletionDate();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('advance.settlement.show', $row->id) . '" rel="tooltip" title="View Advance Settlement Request"><i class="bi bi-eye"></i></a>';
                if ($authUser->can('print', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('advance.request.settlement.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                }
                if ($authUser->can('update', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('advance.settlement.edit', [$row->id]) . '" rel="tooltip" title="Edit Approve Settlement Request">';
                    $btn .= '<i class="bi-pencil-square"></i></a>';

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('advance.settlement.destroy', $row->id) . 'rel="tooltip" title="Delete Advance Settlement">';
                    $btn .= '<i class="bi-trash"></i></a>';
                } else if ($authUser->can('amend', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-danger amend-record"';
                    $btn .= 'data-href = "' . route('advance.settlement.amend', $row->id) . '" data-number="' . $row->getSettlementNumber() . '" title="Reverse Settlement">';
                    $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                }
                return $btn;

            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('AdvanceRequest::Settlement.index');

    }

    /**
     * Show the form for creating a new settlement advance request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $this->authorize('createSettlement', $advanceRequest);

        return view('AdvanceRequest::Settlement.create')
            ->withProjects($this->projects->get())
            ->withAdvanceRequest($advanceRequest);
    }

    /**
     * Store a newly created settlement advance settlement request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\Settlement\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $this->authorize('createSettlement', $advanceRequest);
        $inputs = $request->validated();

        $inputs['advance_request_id'] = $advanceRequestId;
        $inputs['office_id'] = $advanceRequest->office_id;
        $inputs['fiscal_year_id'] = $advanceRequest->fiscal_year_id;
        $inputs['project_code_id'] = $advanceRequest->project_code_id;
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['advance_amount'] = $advanceRequest->getEstimatedAmount();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceRequestSettlement = $this->settlements->create($inputs);
        if ($advanceRequestSettlement) {
            return redirect()->route('advance.settlement.edit', [$advanceRequestSettlement->id])
                ->withSuccessMessage('Advance Request Settlement successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Advance Request Settlement can not be added.');
    }

    /**
     * Show the specified advance request.
     *
     * @param $advanceRequestId
     * @return mixed
     */
    public function show($settlementId)
    {
        $authUser = auth()->user();
        $advanceSettlementRequest = $this->settlements->find($settlementId);
        $advanceRequest = $this->advanceRequests->where('id', '=', $advanceSettlementRequest->advance_request_id)->first();
        return view('AdvanceRequest::Settlement.show')
            ->withAdvanceSettlementRequest($advanceSettlementRequest)
            ->withAdvanceRequest($advanceRequest);
    }

    /**
     * Show the form for editing the specified advance request.
     *
     * @param $advanceRequestSettlementId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($advanceRequestSettlementId)
    {
        $authUser = auth()->user();
        $userCurrentOffice = $authUser->getCurrentOffice();
        $advanceRequestSettlement = $this->settlements->find($advanceRequestSettlementId);
        $this->authorize('update', $advanceRequestSettlement);
        $officeIds = [$authUser->employee->office_id];

        $reviewers = $this->users->permissionBasedUsers('finance-review-advance-settlement');
        if ($userCurrentOffice !== null) {
            if ($userCurrentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                $reviewers = $this->users->permissionBasedUsersByOfficeType('finance-review-advance-settlement', $officeIds);
            }
            if ($userCurrentOffice->office_type_id == config('constant.CLUSTER_OFFICE')) {
                $officeIds[] = $authUser->employee->office->parent_id;
                $reviewers = $this->users->permissionBasedUsersByOfficeType('finance-review-advance-settlement', $officeIds);
            }
            if ($userCurrentOffice->office_type_id == config('constant.DISTRICT_OFFICE')) {
                $officeIds[] = $authUser->employee->office->parent_id;
                $reviewers = $this->users->permissionBasedUsersByOfficeType('finance-review-advance-settlement', $officeIds);
            }
        }

        $approvers = $this->users->getSupervisors($advanceRequestSettlement->requester);
        $projects = $this->projects->getActiveProjectCodes();
        return view('AdvanceRequest::Settlement.edit')
            ->withAuthUser(auth()->user())
            ->withApprovers($approvers)
            ->withProjects($projects)
            ->withAdvanceRequest($advanceRequestSettlement->advanceRequest)
            ->withAdvanceRequestSettlement($advanceRequestSettlement)
            ->withReviewers($reviewers);
    }

    /**
     * Update the specified advance request in storage.
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
        $settlements = $this->settlements->find($id);
        $this->authorize('update', $settlements);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $settlement = $this->settlements->update($id, $inputs);

        if ($settlement) {
            $message = 'Advance request is successfully updated.';
            if ($settlement->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Settlement Advance request is successfully submitted.';
                $settlement->reviewer->notify(new AdvanceSettlementSubmitted($settlement));
            }
            return redirect()->route('advance.settlement.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Settlement Advance Request can not be updated.');
    }

    /**
     * Remove the specified advance request settlement from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $advanceSettlement = $this->settlements->find($id);
        $this->authorize('delete', $advanceSettlement);
        $flag = $this->settlements->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Advance request settlement is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Advance request settlement can not deleted.',
        ], 422);
    }

    public function amend(Request $request, $id)
    {
        $advanceSettlement = $this->settlements->find($id);
        $this->authorize('amend', $advanceSettlement);
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);
        $inputs['status_id'] = config('constant.RETURNED_STATUS');
        $inputs['user_id'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $flag = $this->settlements->amend($advanceSettlement->id, $inputs);
        if ($flag) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Settlement reversed successfully.',
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Settlement cannot be reversed.',
        ], 422);
    }
}
