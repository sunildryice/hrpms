<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelRequest\Notifications\LocalTravelApproved;
use Modules\TravelRequest\Notifications\LocalTravelReturned;
use Modules\TravelRequest\Notifications\LocalTravelRejected;
use Modules\TravelRequest\Notifications\LocalTravelSubmitted;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelRequest\Requests\LocalTravel\Approve\StoreRequest;
use DataTables;
use Modules\TravelRequest\Repositories\LocalTravelRepository;


class LocalTravelApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LocalTravelRepository $localTravels
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository    $employees,
        FiscalYearRepository  $fiscalYears,
        LocalTravelRepository $localTravels,
        UserRepository        $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->localTravels = $localTravels;
        $this->users = $users;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->localTravels->with(['fiscalYear', 'status', 'travelRequest', 'requester'])
                ->select(['*'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.local.travel.reimbursements.create', $row->id) . '" rel="tooltip" title="Approve Local Travel Reimbursement">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::LocalTravel.Approve.index');
    }

    public function create($localTravelId)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravels->find($localTravelId);
        $this->authorize('approve', $localTravel);

        $approvers = $this->users->permissionBasedUsers('approve-recommended-local-travel');

        return view('TravelRequest::LocalTravel.Approve.create')
            ->withApprovers($approvers)
            ->withAuthUser($authUser)
            ->withLocalTravel($localTravel);
    }

    /**
     * Store a newly approved local travel in storage.
     *
     * @param StoreRequest $request
     * @param $localTravelId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request, $localTravelId)
    {
        $localTravel = $this->localTravels->find($localTravelId);
        $this->authorize('approve', $localTravel);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $localTravel = $this->localTravels->approve($localTravel->id, $inputs);

        if ($localTravel) {
            $message = '';
            if ($localTravel->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Local travel reimbursement is successfully returned.';
                $localTravel->requester->notify(new LocalTravelReturned($localTravel));
            } else if ($localTravel->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Local travel reimbursement is successfully rejected.';
                $localTravel->requester->notify(new LocalTravelRejected($localTravel));
            } else if ($localTravel->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Local travel reimbursement is successfully recommended.';
                $localTravel->approver->notify(new LocalTravelSubmitted($localTravel));
            } else {
                $message = 'Local travel reimbursement is successfully approved.';
                $localTravel->requester->notify(new LocalTravelApproved($localTravel));
            }

            return redirect()->route('approve.local.travel.reimbursements.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Local travel reimbursement can not be approved.');
    }
}
