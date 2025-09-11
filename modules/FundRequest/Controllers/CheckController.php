<?php

namespace Modules\FundRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Notifications\FundRequestRejected;
use Modules\FundRequest\Notifications\FundRequestReturned;
use Modules\FundRequest\Notifications\FundRequestSubmitted;
use Modules\FundRequest\Notifications\FundRequestVerified;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\FundRequest\Requests\Check\StoreRequest;
use DataTables;
use Modules\FundRequest\Notifications\FundRequestRecommended;

class CheckController extends Controller
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
                    $q->where('checker_id', $authUser->id);
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
                    $btn .= route('check.fund.requests.create', $row->id) . '" rel="tooltip" title="Check Fund Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('FundRequest::Check.index');
    }

    public function create($fundRequestId)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $certifiers = $this->users->permissionBasedUsers('certify-fund-request');
        // $this->authorize('check', $fundRequest);

        return view('FundRequest::Check.create')
            ->withAuthUser($authUser)
            ->withCertifiers($certifiers)
            ->withFundRequest($fundRequest);
    }

    public function store(StoreRequest $request, $fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        // $this->authorize('check', $fundRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $fundRequest = $this->fundRequests->verify($fundRequest->id, $inputs);

        if ($fundRequest) {
            $message = 'Fund Request Updated';
            if ($fundRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Fund request is successfully returned.';
                $fundRequest->requester->notify(new FundRequestReturned($fundRequest));
            } else if ($fundRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Fund request is successfully rejected.';
                $fundRequest->requester->notify(new FundRequestRejected($fundRequest));
            } else if ($fundRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Fund request is successfully verified.';
                $fundRequest->certifier->notify(new FundRequestVerified($fundRequest));
            } else if($inputs['btn'] == 'save'){
                return redirect()->back()->withInput()->withSuccessMessage($message);            }

            return redirect()->route('check.fund.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Fund request can not be verified.');
    }
}
