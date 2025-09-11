<?php

namespace Modules\FundRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Notifications\FundRequestRejected;
use Modules\FundRequest\Notifications\FundRequestReturned;
use Modules\FundRequest\Notifications\FundRequestSubmitted;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\FundRequest\Requests\Review\StoreRequest;
use DataTables;
use Modules\FundRequest\Notifications\FundRequestRecommended;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param FundRequestRepository $fundRequests
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository      $employees,
        protected FiscalYearRepository    $fiscalYears,
        protected FundRequestRepository   $fundRequests,
        protected UserRepository          $users
    )
    {
    }

    /**
     * Display a listing of the fund requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->fundRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.VERIFIED2_STATUS'));
                })->orWhere(function($q) use($authUser) {
                    $q->whereNull('certifier_id');
                    $q->whereNull('checker_id');
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
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
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('review.fund.requests.create', $row->id) . '" rel="tooltip" title="Review Fund Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('FundRequest::Review.index');
    }

    public function create($fundRequestId)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('review', $fundRequest);
        $approvers = $this->users->permissionBasedUsers('approve-fund-request');

        return view('FundRequest::Review.create', compact('approvers', 'fundRequest', 'authUser'));
    }

    public function store(StoreRequest $request, $fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('review', $fundRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $fundRequest = $this->fundRequests->approve($fundRequest->id, $inputs);

        if ($fundRequest) {
            $message = '';
            if ($fundRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Fund request is successfully returned.';
                $fundRequest->requester->notify(new FundRequestReturned($fundRequest));
            } else if ($fundRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Fund request is successfully rejected.';
                $fundRequest->requester->notify(new FundRequestRejected($fundRequest));
            } else if ($fundRequest->status_id == config('constant.VERIFIED3_STATUS')) {
                $message = 'Fund request is successfully verified.';
                $fundRequest->approver->notify(new FundRequestRecommended($fundRequest));
            }

            return redirect()->route('review.fund.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Fund request can not be reviewed.');
    }
}
