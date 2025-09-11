<?php

namespace Modules\TravelAuthorization\Controllers;

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
use Modules\TravelAuthorization\Notifications\TravelAuthorizationApproved;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationCancelled;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationCancelRejected;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationRejected;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationReturned;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationSubmitted;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationEstimateRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationItineraryRepository;
use Modules\Master\Repositories\TravelTypeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelAuthorization\Requests\TravelAuthorizationReview\StoreRequest;
use DB;
use DataTables;

class TravelAuthorizationApproveController extends Controller
{
    public function __construct(
        protected DsaCategoryRepository $dsaCategory,
        protected DepartmentRepository    $departments,
        protected EmployeeRepository      $employees,
        protected FiscalYearRepository    $fiscalYear,
        protected OfficeRepository        $offices,
        protected ProjectCodeRepository   $projectCodes,
        protected TravelModeRepository   $travelModes,
        protected TravelAuthorizationRepository $travel,
        protected TravelAuthorizationEstimateRepository $travelRequestEstimate,
        protected TravelAuthorizationItineraryRepository $travelRequestItinerary,
        protected StatusRepository        $status,
        protected TravelTypeRepository    $travelTypes,
        protected RoleRepository          $roles,
        protected UserRepository          $user
    )
    {
        $this->destinationPath  = 'travelAuthorization';
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
            $data = $this->travel->with(['requester','status'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->orderBy('request_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->office->getOfficeName();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('request_number', function ($row) {
                    return $row->getTravelAuthorizationNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.ta.requests.create', $row->id) . '" rel="tooltip" title="Approve Travel Authorization">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelAuthorization::Approve.index');
    }

    public function create($id)
    {
        $authUser = auth()->user();
        $travel = $this->travel->find($id);
        $this->authorize('approve', $travel);
        $approvers = $this->user->permissionBasedUsers('approve-recommended-travel-request');

        return view('TravelAuthorization::Approve.create')
            ->withApprovers($approvers)
            ->withAuthUser($authUser)
            ->withTravel($travel);
    }

    public function store(StoreRequest $request, $taId)
    {
        $inputs = $request->validated();
        $travel = $this->travel->find($taId);
        $this->authorize('approve', $travel);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travel = $this->travel->approve($travel->id, $inputs);

        if ($travel) {
            $message = '';

            if ($travel->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Travel Authorization request is successfully returned.';
                 $travel->requester->notify(new TravelAuthorizationReturned($travel));
            } else if($travel->status_id == config('constant.REJECTED_STATUS')){
                $message = 'Travel Authorization request is rejected.';
                $travel->requester->notify(new TravelAuthorizationRejected($travel));
            } else if($travel->status_id == config('constant.RECOMMENDED_STATUS')){
                $message = 'Travel Authorization request is successfully recommended.';
                $travel->approver->notify(new TravelAuthorizationSubmitted($travel));
            } else {
                $message = 'Travel Authorization request is successfully approved.';
                 $travel->requester->notify(new TravelAuthorizationApproved($travel));
            }

            return redirect()->route('approve.ta.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel Authorization request can not be approved.');
    }
}
