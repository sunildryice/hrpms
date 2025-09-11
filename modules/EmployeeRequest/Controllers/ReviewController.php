<?php

namespace Modules\EmployeeRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeRequest\Notifications\EmployeeRequestRecommended;
use Modules\EmployeeRequest\Notifications\EmployeeRequestRejected;
use Modules\EmployeeRequest\Notifications\EmployeeRequestReturned;
use Modules\EmployeeRequest\Repositories\EmployeeRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeRequest\Requests\Review\StoreRequest;
use DataTables;


class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param EmployeeRequestRepository $employeeRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        EmployeeRequestRepository $employeeRequests,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->employeeRequests = $employeeRequests;
        $this->users = $users;
    }

    /**
     * Display a listing of the employee requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->employeeRequests->with(['fiscalYear', 'status', 'dutyStation'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('duty_station', function ($row) {
                    return $row->getDutyStation();
                })->addColumn('type', function ($row) {
                    return $row->getEmployeeType();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('review.employee.requests.create', $row->id) . '" rel="tooltip" title="Review Employee Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('EmployeeRequest::Review.index');
    }

    public function create($employeeRequestId)
    {
        $authUser = auth()->user();
        $employeeRequest = $this->employeeRequests->find($employeeRequestId);
        $this->authorize('review', $employeeRequest);

        $approvers = $this->users->permissionBasedUsers('approve-employee-requisition');

        return view('EmployeeRequest::Review.create')
            ->withAuthUser($authUser)
            ->withApprovers($approvers)
            ->withEmployeeRequest($employeeRequest);
    }

    public function store(StoreRequest $request, $employeeRequestId)
    {
        $employeeRequest = $this->employeeRequests->find($employeeRequestId);
        $this->authorize('review', $employeeRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $employeeRequest = $this->employeeRequests->review($employeeRequest->id, $inputs);

        if ($employeeRequest) {
            $message = '';
            if ($employeeRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Employee request is successfully returned.';
                $employeeRequest->requester->notify(new EmployeeRequestReturned($employeeRequest));
            } else if ($employeeRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Employee request is successfully rejected.';
                $employeeRequest->requester->notify(new EmployeeRequestRejected($employeeRequest));
            } else if ($employeeRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Employee request is successfully verified.';
                $employeeRequest->approver->notify(new EmployeeRequestRecommended($employeeRequest));
            }

            return redirect()->route('review.employee.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Employee request can not be reviewd.');
    }
}
