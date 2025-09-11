<?php

namespace Modules\DistributionRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\DistributionRequest\Notifications\DistributionHandoverApproved;
use Modules\DistributionRequest\Notifications\DistributionHandoverReturned;
use Modules\DistributionRequest\Notifications\DistributionHandoverSent;
use Modules\DistributionRequest\Repositories\DistributionHandoverRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\DistributionRequest\Requests\Approve\StoreRequest;
use Yajra\DataTables\DataTables;


class HandoverApprovedController extends Controller
{
    private $distributionHandovers;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param DistributionHandoverRepository $distributionHandovers
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository             $employees,
        FiscalYearRepository           $fiscalYears,
        DistributionHandoverRepository $distributionHandovers,
        UserRepository                 $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->distributionHandovers = $distributionHandovers;
        $this->users = $users;
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
//            $data = $this->distributionHandovers->getApproved();
            $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

            $query = $this->distributionHandovers->with(['fiscalYear', 'status', 'projectCode', 'district', 'distributionRequest'])
                ->select(['*'])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->whereIn('office_id', $accessibleOfficeIds);

            return DataTables::of($query)
                ->addIndexColumn()
                ->filterColumn('district', function ($query, $keyword) {
                    $query->whereHas('district', function ($q) use ($keyword) {
                        $q->where('district_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('health_facility_name', function ($query, $keyword) {
                    $query->whereHas('healthFacility', function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('requisition_number', function ($query, $keyword) {
                    $query->where('distribution_handover_number', 'like', "%{$keyword}%");
                })
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })->addColumn('project', function ($row) {
                    return $row->distributionRequest->getProjectCodeShortName();
                })->addColumn('requisition_number', function ($row) {
                    return $row->getDistributionHandoverNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.distribution.requests.handovers.show', $row->id) . '" rel="tooltip" title="View Distribution Handover"><i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Distribution Handover"';
                    $btn .= 'href="' . route('distribution.requests.handovers.print', $row->id) . '" target="_blank">';
                    $btn .= '<i class="bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Handover.Approved.index');
    }

    public function show($distributionHandoverId)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);

        return view('DistributionRequest::Handover.Approved.show')
            ->withDistributionHandover($distributionHandover)
            ->withDistributionRequest($distributionHandover->distributionRequest);
    }
}
