<?php

namespace Modules\FundRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Notifications\FundRequestApproved;
use Modules\FundRequest\Notifications\FundRequestCancelApproved;
use Modules\FundRequest\Notifications\FundRequestCancelRejected;
use Modules\FundRequest\Notifications\FundRequestRecommended;
use Modules\FundRequest\Notifications\FundRequestRejected;
use Modules\FundRequest\Notifications\FundRequestReturned;
use Modules\FundRequest\Notifications\FundRequestReturnedToCertifier;
use Modules\FundRequest\Notifications\FundRequestReturnedToChecker;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\FundRequest\Requests\Approve\StoreRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected FundRequestRepository $fundRequests,
        protected UserRepository $users
    ) {
    }

    /**
     * Display a listing of the fund requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->fundRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])->select(['*'])
                // ->where(function ($q) use ($authUser) {
                //     $q->where('reviewer_id', $authUser->id);
                //     $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                // })
                ->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.VERIFIED3_STATUS')]);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->getOfficeName();
                })->addColumn('request_for_office', function ($row) {
                    return $row->getRequestForOfficeName();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('year', function ($row) {
                    return $row->getFiscalYear();
                })->addColumn('month', function ($row) {
                    return $row->getMonthName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.fund.requests.create', $row->id).'" rel="tooltip" title="Approve Fund Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('FundRequest::Approve.index');
    }

    public function create($fundRequestId)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('approve', $fundRequest);

        // $latestTenure = $fundRequest->requester->employee->latestTenure;
        // $supervisors = $this->users->select(['id', 'full_name'])
        //     ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
        //     ->get();
        // $supervisors = $supervisors->reject(function($supervisor) use ($authUser){
        //     return $supervisor->id == $authUser->id;
        // });

        $approvers = $this->users->permissionBasedUsers('approve-recommended-fund-request');

        return view('FundRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withFundRequest($fundRequest)
                // ->withLatestTenure($latestTenure)
                // ->withSupervisors($supervisors);
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('approve', $fundRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $fundRequest = $this->fundRequests->approve($fundRequest->id, $inputs);

        if ($fundRequest) {
            $message = '';
            if ($fundRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Fund request is successfully returned.';
                $fundRequest->requester->notify(new FundRequestReturned($fundRequest));
            } elseif ($fundRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Fund request is successfully rejected.';
                $fundRequest->requester->notify(new FundRequestRejected($fundRequest));
            } elseif ($fundRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Fund request is successfully recommended.';
                $fundRequest->approver->notify(new FundRequestRecommended($fundRequest));
            } elseif ($fundRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Fund request is returned to checker';
                $fundRequest->checker->notify(new FundRequestReturnedToChecker($fundRequest));
            } elseif ($fundRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Fund request is returned to certifier';
                $fundRequest->certifier->notify(new FundRequestReturnedToCertifier($fundRequest));
            } else {
                $message = 'Fund request is successfully approved.';
                $fundRequest->requester->notify(new FundRequestApproved($fundRequest));
            }

            return redirect()->route('approve.fund.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Fund request can not be approved.');
    }

        public function cancelIndex(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->fundRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])->select(['*'])
                ->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->whereIn('status_id', [config('constant.INIT_CANCEL_STATUS')]);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->getOfficeName();
                })->addColumn('request_for_office', function ($row) {
                    return $row->getRequestForOfficeName();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('year', function ($row) {
                    return $row->getFiscalYear();
                })->addColumn('month', function ($row) {
                    return $row->getMonthName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.fund.requests.cancel.create', $row->id).'" rel="tooltip" title="Approve Fund Request Cancellation">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('FundRequest::Cancel.index');
    }

    public function cancelCreate($fundRequestId)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('approveCancel', $fundRequest);

        return view('FundRequest::Cancel.create', compact('authUser', 'fundRequest'));
    }

    public function cancelStore(StoreRequest $request, $fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('approveCancel', $fundRequest);
        $inputs = $request->validated();

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $fundRequest = $this->fundRequests->approve($fundRequest->id, $inputs);

        if ($fundRequest) {
            $message = '';
            if ($fundRequest->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Cancellation fund request rejected.';
                $fundRequest->requester->notify(new FundRequestCancelRejected($fundRequest));
            } else {
                $message = 'Cancellation of fund request is approved.';
                $fundRequest->requester->notify(new FundRequestCancelApproved($fundRequest));
            }

            return redirect()->route('approve.fund.requests.cancel.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Fund request cannot be updated.');
    }

}
