<?php

namespace Modules\FundRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApprovedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $fundRequests;
    private $users;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param FundRequestRepository $fundRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        FundRequestRepository $fundRequests,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->fundRequests = $fundRequests;
        $this->users = $users;
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
            $data = $this->fundRequests->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->getOfficeName();
                })->addColumn('request_for_office', function ($row) {
                return $row->getRequestForOfficeName();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('approved_date', function ($row) {
                return $row->approvedLog?->getCreatedDate();
            })->addColumn('year', function ($row) {
                return $row->getFiscalYear();
            })->addColumn('month', function ($row) {
                return $row->getMonthName();
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('approved.fund.requests.show', $row->id) . '" rel="tooltip" title="View Fund Request">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                if ($authUser->can('print', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.fund.requests.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                }
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('FundRequest::Approved.index');
    }

    /**
     * Show the specified fund request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id) {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($id);
        $this->authorize('print', $fundRequest);

        return view('FundRequest::print')
            ->withFundRequest($fundRequest);
    }

    /**
     * Show the specified fund request.
     *
     * @param $fundRequestId
     * @return mixed
     */
    public function show($fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        $this->authorize('viewApproved', $fundRequest);
        return view('FundRequest::Approved.show')
            ->withFundRequest($fundRequest);
    }
}
