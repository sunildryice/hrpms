<?php

namespace Modules\DistributionRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\DistributionRequest\Notifications\DistributionRequestApproved;
use Modules\DistributionRequest\Notifications\DistributionRequestRejected;
use Modules\DistributionRequest\Notifications\DistributionRequestReturned;
use Modules\DistributionRequest\Notifications\DistributionRequestSubmitted;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\DistributionRequest\Requests\Approve\StoreRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected DistributionRequestRepository $distributionRequests,
        protected UserRepository $users
    ) {}

    /**
     * Display a listing of the distribution requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->distributionRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })->addColumn('project', function ($row) {
                    return $row->getProjectCode();
                })->addColumn('requisition_number', function ($row) {
                    return $row->getDistributionRequestNumber();
                })->addColumn('health_facility_id', function ($row) {
                    return $row->getHealthFacility();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.distribution.requests.create', $row->id).'" rel="tooltip" title="Approve Distribution Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Approve.index');
    }

    public function create($distributionRequestId)
    {
        $authUser = auth()->user();
        $distributionRequest = $this->distributionRequests->find($distributionRequestId);
        $this->authorize('approve', $distributionRequest);

        // $latestTenure = $distributionRequest->requester->employee->latestTenure;
        $latestTenure = auth()->user()->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name', 'employee_id'])
            ->whereIn('employee_id', [$latestTenure->supervisor_id, $latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        return view('DistributionRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withDistributionRequest($distributionRequest)
            ->withSupervisors($supervisors);
    }

    public function store(StoreRequest $request, $distributionRequestId)
    {
        $distributionRequest = $this->distributionRequests->find($distributionRequestId);
        $this->authorize('approve', $distributionRequest);
        $inputs = $request->validated();
        $items = $distributionRequest->distributionRequestItems()->with('inventoryItem')->get();

        if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
            foreach ($items->groupBy('inventory_item_id') as  $distItems) {
                if ($distItems->first()->inventoryItem->getAvailableQuantity()  < $distItems->sum('quantity')) {
                    return redirect()->back()
                        ->withInput()
                        ->withWarningMessage('Distribution request can not be approved. Assigned quantity is less than requested quantity for item'.' '.$distItems->first()?->inventoryItem?->item?->title);
                }
            }
        }

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionRequest = $this->distributionRequests->approve($distributionRequest->id, $inputs);

        if ($distributionRequest) {
            $message = '';
            if ($distributionRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Distribution request is successfully returned.';
                $distributionRequest->requester->notify(new DistributionRequestReturned($distributionRequest));
            } elseif ($distributionRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Distribution request is successfully rejected.';
                $distributionRequest->requester->notify(new DistributionRequestRejected($distributionRequest));
            } elseif ($distributionRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Distribution request is successfully recommended.';
                $distributionRequest->approver->notify(new DistributionRequestSubmitted($distributionRequest));
            } else {
                $message = 'Distribution request is successfully approved.';
                $distributionRequest->requester->notify(new DistributionRequestApproved($distributionRequest));
            }

            return redirect()->route('approve.distribution.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Distribution request can not be approved.');
    }
}
