<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\AdvanceRequest\Notifications\AdvanceSettlementApproved;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementForwarded;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementReturned;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\AdvanceRequest\Requests\Approve\StoreRequest;
use DataTables;


class ApproveSettlementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param SettlementRepository $settlements
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository       $employees,
        FiscalYearRepository     $fiscalYears,
        AdvanceRequestRepository $advanceRequests,
        SettlementRepository     $settlements,
        UserRepository           $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->settlements = $settlements;
        $this->users = $users;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->settlements->with(['status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->whereIn('status_id', [config('constant.VERIFIED2_STATUS'), config('constant.RECOMMENDED_STATUS')]);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
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
                    $btn .= route('approve.advance.settlements.create', $row->id) . '" rel="tooltip" title="Approve Settlement  Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Settlement.Approve.index');
    }

    /**
     * Display a approval form for specified advance settlement
     *
     * @param $settlementId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($settlementId)
    {
        $authUser = auth()->user();
        $advanceSettlementRequest = $this->settlements->find($settlementId);
        $advanceRequest = $this->advanceRequests->find($advanceSettlementRequest->advance_request_id);
        $this->authorize('approve', $advanceSettlementRequest);

        $approvers = $this->users->permissionBasedUsers('approve-recommended-advance-settlement');
        $approvers = $approvers ? $approvers->reject(function ($supervisor) use ($authUser) {
            return $authUser->id == $supervisor->id;
        }) : collect();
        return view('AdvanceRequest::Settlement.Approve.create')
            ->withAuthUser($authUser)
            ->withAdvanceRequest($advanceRequest)
            ->withAdvanceSettlementRequest($advanceSettlementRequest)
            ->withApprovers($approvers);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param StoreRequest $request
     * @param $settlementId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request, $settlementId)
    {
        $advanceSettlementRequest = $this->settlements->find($settlementId);
        $this->authorize('approve', $advanceSettlementRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceSettlementRequest = $this->settlements->approve($advanceSettlementRequest->id, $inputs);

        if ($advanceSettlementRequest) {
            $message = '';
            if ($advanceSettlementRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Advance request is successfully returned.';
                $advanceSettlementRequest->requester->notify(new AdvanceSettlementReturned($advanceSettlementRequest));
            } else if ($advanceSettlementRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Advance request is successfully recommended.';
                $advanceSettlementRequest->approver->notify(new AdvanceSettlementForwarded($advanceSettlementRequest));
            } else if ($advanceSettlementRequest->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Advance Settlement Request is successfully approved.';
                $advanceSettlementRequest->requester->notify(new AdvanceSettlementApproved($advanceSettlementRequest));
            }

            return redirect()->route('approve.advance.settlements.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Settlement Advance  request can not be approved.');
    }
}
