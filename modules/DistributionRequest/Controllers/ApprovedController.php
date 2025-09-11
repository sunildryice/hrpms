<?php

namespace Modules\DistributionRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $distributionRequests;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param DistributionRequestRepository $distributionRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository              $employees,
        FiscalYearRepository            $fiscalYears,
        DistributionRequestRepository   $distributionRequests,
        UserRepository                  $users
    )
    {
        $this->employees            = $employees;
        $this->fiscalYears          = $fiscalYears;
        $this->distributionRequests = $distributionRequests;
        $this->users                = $users;
    }

    /**
     * Display a listing of the distribution requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

            $query = $this->distributionRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])
                ->select(['*'])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->whereIn('office_id', $accessibleOfficeIds);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('district', function ($row){
                    return $row->getDistrictName();
                })
                ->filterColumn('district', function($query, $keyword) {
                    $query->whereHas('district', function($q) use ($keyword) {
                        $q->where('district_name', 'like', "%{$keyword}%");
                    });
                })->filterColumn('health_facility_name', function($query, $keyword) {
                    $query->whereHas('healthFacility', function($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    });
                })->addColumn('health_facility_name', function ($row) {
                    return $row->getHealthFacility();
                })->addColumn('project', function ($row){
                    return $row->getProjectCodeShortName();
                })->addColumn('requisition_number', function ($row){
                    return $row->getDistributionRequestNumber();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.distribution.requests.show', $row->id) . '" rel="tooltip" title="View Distribution Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('createHandover', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm create-handover-distribution" rel="tooltip" title="Create Handover" ';
                        $btn .= 'data-href="' . route('distribution.requests.handovers.store', $row->id) . '">';
                        $btn .= '<i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('DistributionRequest::Approved.index');
    }

    /**
     * Show the specified distribution request.
     *
     * @param $distributionRequestId
     * @return mixed
     */
    public function show($distributionRequestId)
    {
        $distributionRequest = $this->distributionRequests->find($distributionRequestId);
        $this->authorize('viewApproved', $distributionRequest);
        return view('DistributionRequest::Approved.show')
            ->withDistributionRequest($distributionRequest);
    }
}
