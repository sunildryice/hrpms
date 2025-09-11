<?php

namespace Modules\DistributionRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\DistributionRequest\Notifications\DistributionHandoverApproved;
use Modules\DistributionRequest\Notifications\DistributionHandoverDistributed;
use Modules\DistributionRequest\Notifications\DistributionHandoverReceived;
use Modules\DistributionRequest\Notifications\DistributionHandoverReturned;
use Modules\DistributionRequest\Repositories\DistributionHandoverRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\DistributionRequest\Requests\Receive\UpdateRequest;
use DataTables;


class HandoverReceivedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param DistributionHandoverRepository $distributionHandovers
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        DistributionHandoverRepository $distributionHandovers,
        UserRepository            $users
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
            $data = $this->distributionHandovers->getReceived();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function ($row){
                    return $row->getDistrictName();
                })->addColumn('project', function ($row){
                    return $row->getProjectCode();
                })->addColumn('requisition_number', function ($row){
                    return $row->getDistributionHandoverNumber();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('received.distribution.requests.handovers.show', $row->id) . '" rel="tooltip" title="View Distribution Handover"><i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Distribution Handover"';
                    $btn .= 'href="' . route('distribution.requests.handovers.print', $row->id) . '" target="_blank">';
                    $btn .= '<i class="bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Handover.Received.index');
    }

    public function show($distributionHandoverId)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);

        return view('DistributionRequest::Handover.Received.show')
            ->withDistributionHandover($distributionHandover)
            ->withDistributionRequest($distributionHandover->distributionRequest);
    }
   

}
