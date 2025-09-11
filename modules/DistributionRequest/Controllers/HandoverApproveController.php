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
use DataTables;


class HandoverApproveController extends Controller
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
            $data = $this->distributionHandovers->with(['status', 'projectCode', 'district'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

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
                    $btn .= route('approve.distribution.requests.handovers.create', $row->id) . '" rel="tooltip" title="Approve Distribution Handover">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Handover.Approve.index');
    }

    public function create($distributionHandoverId)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);
        $this->authorize('approve', $distributionHandover);

        return view('DistributionRequest::Handover.Approve.create')
            ->withAuthUser($authUser)
            ->withDistributionHandover($distributionHandover)
            ->withDistributionRequest($distributionHandover->distributionRequest);
    }

    public function store(StoreRequest $request, $distributionHandoverId)
    {
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);
        $this->authorize('approve', $distributionHandover);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionHandover = $this->distributionHandovers->approve($distributionHandover->id, $inputs);

        if ($distributionHandover) {
            $message = '';
            if ($distributionHandover->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Distribution handover is successfully returned.';
                $distributionHandover->requester->notify(new DistributionHandoverReturned($distributionHandover));
            } else {
                $message = 'Distribution handover is successfully approved.';
                $distributionHandover->requester->notify(new DistributionHandoverApproved($distributionHandover));
                $distributionHandover->receiver->notify(new DistributionHandoverSent($distributionHandover));
            }

            return redirect()->route('approve.distribution.requests.handovers.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Distribution handover can not be approved.');
    }
}
