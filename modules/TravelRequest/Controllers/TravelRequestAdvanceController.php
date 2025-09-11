<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DsaCategoryRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\Master\Repositories\TravelTypeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Notifications\TravelRequestAdvanceApproved;
use Modules\TravelRequest\Notifications\TravelRequestReturned;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\Advance\Finance\StoreRequest;

class TravelRequestAdvanceController extends Controller
{
    protected $destinationPath;

    public function __construct(
        protected DsaCategoryRepository $dsaCategory,
        protected DepartmentRepository $departments,
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYear,
        protected OfficeRepository $offices,
        protected ProjectCodeRepository $projectCodes,
        protected TravelModeRepository $travelModes,
        protected TravelRequestRepository $travelRequest,
        protected TravelRequestEstimateRepository $travelRequestEstimate,
        protected TravelRequestItineraryRepository $travelRequestItinerary,
        protected StatusRepository $status,
        protected TravelTypeRepository $travelTypes,
        protected RoleRepository $roles,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'travelrequest';
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelRequest->with(['requester', 'status'])->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                ->whereNotNull('advance_requested_at')
                ->whereNull('advance_received_at')
                ->whereDoesntHave('travelClaim')
                ->orderBy('departure_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->getReturnDate();
                })->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.travel.requests.advance.create', $row->id).'" rel="tooltip" title="Travel Request Advance">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::Advance.index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($id);
        $this->authorize('giveAdvance', $travelRequest);

        return view('TravelRequest::Advance.create')
            ->with([
                'authUser' => ($authUser),
                'travelRequest' => ($travelRequest),
            ]);
    }

    public function store(StoreRequest $request, $travelRequestId)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('giveAdvance', $travelRequest);
        $inputs['finance_user_id'] = auth()->id();
        $travelRequest = $this->travelRequest->storeAdvance($travelRequest->id, $inputs);

        if ($travelRequest) {
            $message = 'Travel request is advance assigned.';
            $travelRequest->requester->notify(new TravelRequestAdvanceApproved($travelRequest));

            return redirect()->route('approve.travel.requests.advance.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Advance amount cannot be updated');
    }
}
