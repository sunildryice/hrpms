<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelRequest\Notifications\TravelReportApproved;
use Modules\TravelRequest\Notifications\TravelReportReturned;
use Modules\TravelRequest\Repositories\TravelReportRepository;
use Modules\TravelRequest\Repositories\TravelReportRecommendationRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelRequest\Requests\TravelReportReview\StoreRequest;
use DataTables;

class TravelReportReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees ,
     * @param TravelReportRepository $travelReport ,
     * @param TravelReportRecommendationRepository $travelReportRecommendation ,
     * @param TravelRequestRepository $travelRequest ,
     * @param TravelRequestEstimateRepository $TravelRequestEstimateRepository ,
     * @param TravelRequestItineraryRepository $travelRequestItineraryRepository ,
     * @param RoleRepository $roles ,
     * @param StatusRepository $status ,
     * @param UserRepository $user
     *
     */

    public function __construct(
        EmployeeRepository                   $employees,
        TravelReportRepository               $travelReport,
        TravelReportRecommendationRepository $travelReportRecommendation,
        TravelRequestRepository              $travelRequest,
        TravelRequestEstimateRepository      $travelRequestEstimate,
        TravelRequestItineraryRepository     $travelRequestItinerary,
        RoleRepository                       $roles,
        StatusRepository                     $status,
        UserRepository                       $user
    )
    {
        $this->employees = $employees;
        $this->travelReport = $travelReport;
        $this->travelReportRecommendation = $travelReportRecommendation;
        $this->travelRequest = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->roles = $roles;
        $this->status = $status;
        $this->user = $user;
        $this->destinationPath = 'travelreport';
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
            $data = $this->travelReport->with(['travelRequest'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->travelRequest->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->travelRequest->getReturnDate();
                })->addColumn('final_destination', function ($row) {
                    return $row->travelRequest->final_destination;
                })->addColumn('travel_number', function ($row) {
                    return $row->travelRequest->getTravelRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getReporterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.travel.reports.create', $row->id) . '" rel="tooltip" title="Approve Travel Report">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelReportReview.index');
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
        $travelReport = $this->travelReport->find($id);
        $this->authorize('approve', $travelReport);

        return view('TravelRequest::TravelReportReview.create')
            ->withAuthUser($authUser)
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest)
            ->withRoles($this->roles->get());
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $travelReport = $this->travelReport->find($id);
        $this->authorize('approve', $travelReport);

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelReport = $this->travelReport->approve($travelReport->id, $inputs);

        if ($travelReport) {
            $message = '';
            if ($travelReport->status_id == 2) {
                $message = 'Travel report is successfully returned.';
                $travelReport->reporter->notify(new TravelReportReturned($travelReport));
            } else {
                $message = 'Travel report is successfully approved.';
                $travelReport->reporter->notify(new TravelReportApproved($travelReport));
            }

            return redirect()->route('approve.travel.reports.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel Report can not be approved.');
    }
}
