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


class HandoverReceiveController extends Controller
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
                    $q->where('receiver_id', $authUser->id);
                    $q->whereIn('status_id',[config('constant.APPROVED_STATUS'),config('constant.RECEIVED_STATUS')]);
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
                    $btn .= route('receive.distribution.requests.handovers.edit', $row->id) . '" rel="tooltip" title="Receive Distribution Handover">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Handover.Receive.index');
    }

    public function edit($distributionHandoverId)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);
        $this->authorize('receive', $distributionHandover);
        $receivedDate = $distributionHandover->received_date?->format('Y-m-d');

        return view('DistributionRequest::Handover.Receive.edit')
            ->withAuthUser($authUser)
            ->withDistributionHandover($distributionHandover)
            ->withReceivedDate($receivedDate)
            ->withDistributionRequest($distributionHandover->distributionRequest);
    }

    public function update(UpdateRequest $request, $distributionHandoverId)
    {
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);
        $this->authorize('receive', $distributionHandover);
        $currentStatus = $distributionHandover->status_id;
        $inputs = $request->validated();
        if($inputs['handover_date']){
            $receivedDate = strtotime($inputs['received_date']);
            $handoverDate = strtotime($inputs['handover_date']);
            if($handoverDate<$receivedDate){
                return redirect()->back()
                ->withInput()
                ->withWarningMessage('Handover date cannot be less than received date.');
            }
        }
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionHandover = $this->distributionHandovers->receive($distributionHandover->id, $inputs);

        if ($distributionHandover) {
            $message = 'Distribution handover is successfully updated.';
            if ($distributionHandover->status_id == config('constant.RECEIVED_STATUS') && $currentStatus != $distributionHandover->status_id) {
                $message = 'Distribution handover is successfully received.';
                $distributionHandover->requester->notify(new DistributionHandoverReceived($distributionHandover));
            } else if($distributionHandover->status_id == config('constant.DISTRIBUTED_STATUS')) {
                $message = 'Distribution handover is successfully distributed.';
                $distributionHandover->requester->notify(new DistributionHandoverDistributed($distributionHandover));
                $distributionHandover->approver->notify(new DistributionHandoverDistributed($distributionHandover));
            }

            return redirect()->route('receive.distribution.requests.handovers.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Distribution handover can not be approved.');
    }

   

}
