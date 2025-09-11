<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\GoodRequestForwarded;
use Modules\GoodRequest\Notifications\GoodRequestRejected;
use Modules\GoodRequest\Notifications\GoodRequestReturned;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\GoodRequest\Requests\Review\StoreRequest;
use DataTables;


class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GoodRequestRepository $goodRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        goodRequestRepository $goodRequests,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->goodRequests = $goodRequests;
        $this->users = $users;
    }

    /**
     * Display a listing of the good requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->with(['status', 'requester'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('review.good.requests.create', $row->id) . '" rel="tooltip" title="Review Good Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::Review.index');
    }

    public function create($goodRequestId)
    {
        $authUser = auth()->user();
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('review', $goodRequest);
        $approvers = $this->users->permissionBasedUsers('approve-good-request');
        $approvers = $approvers->reject(function($approver) use ($authUser){
           return $approver->id == $authUser->id;
        });

        return view('GoodRequest::Review.create')
            ->withAuthUser($authUser)
            ->withgoodRequest($goodRequest)
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $goodRequestId)
    {
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('review', $goodRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->review($goodRequest->id, $inputs);

        if($goodRequest){
            $message = '';
            if ($goodRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Good request is successfully returned.';
                $goodRequest->requester->notify(new GoodRequestReturned($goodRequest));
            } else if ($goodRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Good request is successfully rejected.';
                $goodRequest->requester->notify(new GoodRequestRejected($goodRequest));
            } else if ($goodRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Good request is successfully verified.';
                $goodRequest->approver->notify(new GoodRequestForwarded($goodRequest));
            }

            return redirect()->route('review.good.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Good request can not be approved.');
    }
}
