<?php

namespace Modules\EmployeeRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeRequest\Repositories\EmployeeRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $employees;
    private $employeeRequests;
    private $fiscalYears;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param EmployeeRequestRepository $employeeRequests
     * @param FiscalYearRepository $fiscalYears
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        EmployeeRequestRepository $employeeRequests,
        FiscalYearRepository      $fiscalYears,
        UserRepository            $users
    )
    {
        $this->employees        = $employees;
        $this->employeeRequests = $employeeRequests;
        $this->fiscalYears      = $fiscalYears;
        $this->users            = $users;
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
            $data = $this->employeeRequests->getApproved();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('approved_date', function ($row) {
                    return $row->getApprovedDate();
                })
                ->addColumn('duty_station', function ($row) {
                    return $row->getDutyStation();
                })->addColumn('type', function ($row) {
                    return $row->getEmployeeType();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.employee.requests.show', $row->id) . '" rel="tooltip" title="View Employee Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.employee.requests.print', $row->id) . '" target="_blank" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('EmployeeRequest::Approved.index');
    }

    /**
     * Show the specified advance request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $employeeRequest = $this->employeeRequests->find($id);
        $this->authorize('print', $employeeRequest);
        $employeeRequest = $this->employeeRequests ->select('*')
                                ->with('fiscalYear','status','approver','requester','reviewer')
                                ->where('id', $id)
                                ->whereStatusId(config('constant.APPROVED_STATUS'))
                                ->first();
        $approver = $this->employees->select('*')->where('id', $employeeRequest->approver->employee_id)->first();
        $requester = $this->employees->select('*')->where('id', $employeeRequest->requester->employee_id)->first();
        $reviewer = $this->employees->select('*')->where('id', $employeeRequest->reviewer->employee_id)->first();
        $date['required_date'] = Carbon::createFromFormat('Y-m-d', $employeeRequest->required_date)->toFormattedDateString();
        $date['tentative_submission_date'] = $employeeRequest->tentative_submission_date?Carbon::createFromFormat('Y-m-d', $employeeRequest->tentative_submission_date)->toFormattedDateString():'';
        foreach($employeeRequest->logs as $log){
            if($log->status_id == 3 ){
                $date['submitted_date'] = $log->created_at->toFormattedDateString();
            }
            if($log->status_id == 6 ){
                $date['approved_date'] = $date['recommended_date'] = $log->created_at->toFormattedDateString();
            }
            if($log->status_id == 4 || $log->status_id == 5){
                $date['recommended_date'] = $log->created_at->toFormattedDateString();
            }
        }

        return view('EmployeeRequest::print')
            ->withEmployeeRequest($employeeRequest)
            ->withApprover($approver)
            ->withDates($date)
            ->withRequester($requester)
            ->withReviewer($reviewer);
    }

    /**
     * Show the specified employee request.
     *
     * @param $employeeRequestId
     * @return mixed
     */
    public function show($employeeRequestId)
    {
        $employeeRequest = $this->employeeRequests->find($employeeRequestId);
        $this->authorize('viewApproved', $employeeRequest);
        return view('EmployeeRequest::Approved.show')
            ->withEmployeeRequest($employeeRequest);
    }
}
