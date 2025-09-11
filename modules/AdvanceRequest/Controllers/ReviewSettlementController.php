<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\AdvanceRequest\Notifications\AdvanceSettlementForwarded;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementReturned;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\AdvanceRequest\Requests\Settlement\Review\StoreRequest;
use DataTables;
use Modules\AdvanceRequest\Notifications\AdvanceSettlementVerified;

class ReviewSettlementController extends Controller
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
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
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
                    $btn .= route('review.advance.settlements.create', $row->id) . '" rel="tooltip" title="Review Settlement  Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Settlement.Review.index');
    }

    public function create($advanceSettlementId)
    {
        $authUser = auth()->user();
        $advanceSettlement = $this->settlements->find($advanceSettlementId);
        $advanceRequest = $this->advanceRequests->find($advanceSettlement->advance_request_id);
        $this->authorize('review', $advanceSettlement);

        $requesterCurrentOffice = $advanceSettlement->requester->getCurrentOffice();
        $verifiers = null;
        $officeIds = [$authUser->employee->office_id];
        if ($requesterCurrentOffice !== null) {
            if ($requesterCurrentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                $verifiers = null;
            }
            if ($requesterCurrentOffice->office_type_id == config('constant.CLUSTER_OFFICE')) {
                $officeIds[] = $authUser->employee->office->parent_id;
                $verifiers = $this->users->permissionBasedUsersByOfficeType('finance-review-advance-settlement', $officeIds);
            }
            if ($requesterCurrentOffice->office_type_id == config('constant.DISTRICT_OFFICE')) {
                $officeIds[] = $authUser->employee->office->parent_id;
                $verifiers = $this->users->permissionBasedUsersByOfficeType('finance-review-advance-settlement', $officeIds);
            }
        }

        return view('AdvanceRequest::Settlement.Review.create')
            ->withAuthUser($authUser)
            ->withAdvanceRequest($advanceRequest)
            ->withAdvanceSettlementRequest($advanceSettlement)
            ->withVerifiers($verifiers);
    }

    public function store(StoreRequest $request, $advanceSettlementId)
    {
        $advanceSettlement = $this->settlements->find($advanceSettlementId);
        $this->authorize('review', $advanceSettlement);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $advanceSettlement = $this->settlements->review($advanceSettlement->id, $inputs);
        if ($advanceSettlement) {
            $message = '';
            if ($advanceSettlement->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Advance request settlement is successfully returned.';
                $advanceSettlement->requester->notify(new AdvanceSettlementReturned($advanceSettlement));
            } else {
                if ($inputs['verifier_id']) {
                    $message = 'Advance request settlement is forwarded for verification.';
                    $advanceSettlement->verifier->notify(new AdvanceSettlementVerified($advanceSettlement));
                } else {
                    $message = 'Advance request settlement is forwarded for approval.';
                    $advanceSettlement->approver->notify(new AdvanceSettlementForwarded($advanceSettlement));
                }
            }

            return redirect()->route('review.advance.settlements.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Advance request settlement can not be reviewed.');
    }
}
