<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Master\Repositories\DsaCategoryRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\TravelRequest\Notifications\TravelRequestApproved;
use Modules\TravelRequest\Notifications\TravelRequestCancelled;
use Modules\TravelRequest\Notifications\TravelRequestCancelRejected;
use Modules\TravelRequest\Notifications\TravelRequestRejected;
use Modules\TravelRequest\Notifications\TravelRequestReturned;
use Modules\TravelRequest\Notifications\TravelRequestSubmitted;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\Master\Repositories\TravelTypeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelRequest\Requests\TravelRequestReview\StoreRequest;
use DB;
use DataTables;

class TravelRequestApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DsaCategoryRepository $dsaCategory,
     * @param DepartmentRepository    $departments,
     * @param EmployeeRepository      $employees,
     * @param FiscalYearRepository    $fiscalYear,
     * @param OfficeRepository        $offices,
     * @param ProjectCodeRepository   $projectCodes,
     * @param StatusRepository        $status,
     * @param TravelRequestRepository $travelRequest,
     * @param TravelRequestEstimateRepository $travelRequestEstimate,
     * @param TravelRequestItineraryRepository $travelRequestItinerary,
     * @param TravelModeRepository   $travelModes,
     * @param TravelTypeRepository    $travelTypes,
     * @param RoleRepository          $roles,
     * @param UserRepository          $user
     *
     */
    public function __construct(
        DsaCategoryRepository $dsaCategory,
        DepartmentRepository    $departments,
        EmployeeRepository      $employees,
        FiscalYearRepository    $fiscalYear,
        OfficeRepository        $offices,
        ProjectCodeRepository   $projectCodes,
        TravelModeRepository   $travelModes,
        TravelRequestRepository $travelRequest,
        TravelRequestEstimateRepository $travelRequestEstimate,
        TravelRequestItineraryRepository $travelRequestItinerary,
        StatusRepository        $status,
        TravelTypeRepository    $travelTypes,
        RoleRepository          $roles,
        UserRepository          $user
    )
    {
        $this->dsaCategory      = $dsaCategory;
        $this->departments      = $departments;
        $this->employees        = $employees;
        $this->fiscalYear       = $fiscalYear;
        $this->offices          = $offices;
        $this->projectCodes     = $projectCodes;
        $this->status           = $status;
        $this->travelRequest    = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->travelModes      = $travelModes;
        $this->travelTypes      = $travelTypes;
        $this->roles            = $roles;
        $this->user             = $user;
        $this->destinationPath  = 'travelrequest';
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelRequest->with(['requester','status'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->orderBy('departure_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row){
                    return $row->getRequesterName();
                })->addColumn('departure_date', function ($row){
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row){
                    return $row->getReturnDate();
                })->addColumn('total_days', function ($row){
                    return $row->getTotalDays();
                })->addColumn('travel_number', function ($row){
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.travel.requests.create', $row->id) . '" rel="tooltip" title="Approve Travel Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelRequestApprove.index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($id);
        $this->authorize('approve', $travelRequest);
        $approvers = $this->user->permissionBasedUsers('approve-recommended-travel-request');

        return view('TravelRequest::TravelRequestApprove.create')
            ->withApprovers($approvers)
            ->withAuthUser($authUser)
            ->withTravelRequest($travelRequest);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $travelRequestId)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('approve', $travelRequest);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelRequest = $this->travelRequest->approve($travelRequest->id, $inputs);

        if ($travelRequest) {
            $message = '';

            if ($travelRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Travel request is successfully returned.';
                 $travelRequest->requester->notify(new TravelRequestReturned($travelRequest));
            } else if($travelRequest->status_id == config('constant.REJECTED_STATUS')){
                $message = 'Travel request is rejected.';
                $travelRequest->requester->notify(new TravelRequestRejected($travelRequest));
            } else if($travelRequest->status_id == config('constant.RECOMMENDED_STATUS')){
                $message = 'Travel request is successfully recommended.';
                $travelRequest->approver->notify(new TravelRequestSubmitted($travelRequest));
            } else {
                $message = 'Travel request is successfully approved.';
                 $travelRequest->requester->notify(new TravelRequestApproved($travelRequest));
            }

            return redirect()->route('approve.travel.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel request can not be approved.');
    }

    public function cancelIndex(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelRequest->with(['requester','status'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->where('status_id', config('constant.INIT_CANCEL_STATUS'))
                ->orderBy('departure_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row){
                    return $row->getRequesterName();
                })->addColumn('departure_date', function ($row){
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row){
                    return $row->getReturnDate();
                })->addColumn('total_days', function ($row){
                    return $row->getTotalDays();
                })->addColumn('travel_number', function ($row){
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.travel.requests.cancel.create', $row->id) . '" rel="tooltip" title="Approve Travel Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelRequestApprove.cancel.index');
    }

    public function cancelCreate($id)
    {
        $travelRequest = $this->travelRequest->find($id);
        $this->authorize('approveCancel', $travelRequest);
        return view('TravelRequest::TravelRequestApprove.cancel.create')->with('travelRequest', $travelRequest);
    }

    public function cancel(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequest->find($id);
        $this->authorize('approveCancel', $travelRequest);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['updated_by'] = auth()->id();
        $inputs['cancelled_at'] = date('Y-m-d H:i:s');

        if($inputs['status_id'] == config('constant.REJECTED_STATUS')){
            $inputs['status_id'] = config('constant.APPROVED_STATUS');
            $inputs['cancel_remarks'] = null;
        }

        $travelRequest = $this->travelRequest->cancel($travelRequest->id, $inputs);

        if ($travelRequest) {
            $message = '';
            if ($travelRequest->status_id == config('constant.CANCELLED_STATUS')) {
                $message = 'Cancellation of Travel request is successfully approved.';
                 $travelRequest->requester->notify(new TravelRequestCancelled($travelRequest));
            } else {
                 $message = 'Travel request cancel is rejected.';
                $travelRequest->requester->notify(new TravelRequestCancelRejected($travelRequest));
            }

            return redirect()->route('approve.travel.requests.cancel.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel request cancel can not be approved.');
    }
}
