<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $goodRequests;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GoodRequestRepository $GoodRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        GoodRequestRepository $GoodRequests,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->goodRequests = $GoodRequests;
        $this->users = $users;
    }

    /**
     * Display a listing of the Good requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->getApproved();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('request_number', function ($row) {
                return $row->getGoodRequestNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('good.requests.show', $row->id) . '" rel="tooltip" title="View Good Request"><i class="bi bi-eye"></i></a>';
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
        }

        return view('GoodRequest::Approved.index');
    }

    /**
     * Show the specified good request.
     *
     * @param $goodRequestId
     * @return mixed
     */
    public function show($goodRequestId)
    {
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('viewApproved', $goodRequest);
        return view('GoodRequest::Approved.show')
            ->withGoodRequest($goodRequest);
    }
}
