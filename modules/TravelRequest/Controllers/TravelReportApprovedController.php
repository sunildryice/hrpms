<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelRequest\Repositories\TravelReportRepository;
use Modules\TravelRequest\Repositories\TravelReportRecommendationRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class TravelReportApprovedController extends Controller
{
    private $travelReport;
    private $employees;
    private $travelReportRecommendation;
    private $travelRequest;
    private $travelRequestEstimate;
    private $travelRequestItinerary;
    private $roles;
    private $status;
    private $user;
    private $destinationPath;

    /**
     * Create a new controller instance.
     * @param EmployeeRepository $employees
     * @param RoleRepository $roles
     * @param StatusRepository $status
     * @param TravelReportRepository $travelReport
     * @param TravelReportRecommendationRepository $travelReportRecommendation
     * @param TravelRequestRepository $travelRequest
     * @param TravelRequestEstimateRepository $travelRequestEstimate
     * @param TravelRequestItineraryRepository $travelRequestItinerary
     * @param UserRepository $user
     */
    public function __construct(
        EmployeeRepository $employees,
        RoleRepository $roles,
        StatusRepository $status,
        TravelReportRepository $travelReport,
        TravelReportRecommendationRepository $travelReportRecommendation,
        TravelRequestRepository $travelRequest,
        TravelRequestEstimateRepository $travelRequestEstimate,
        TravelRequestItineraryRepository $travelRequestItinerary,
        UserRepository $user
    ) {
        $this->destinationPath = 'travelreport';
        $this->employees = $employees;
        $this->roles = $roles;
        $this->status = $status;
        $this->travelReport = $travelReport;
        $this->travelReportRecommendation = $travelReportRecommendation;
        $this->travelRequest = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->user = $user;
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
            $data = $this->travelReport->getApproved();

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
                    $btn .= route('approved.travel.reports.show', $row->id) . '" rel="tooltip" title="View Travel Report">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('travel.report.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelReportApproved.index');
    }

    /**
     * Show the specified advance request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $travelReport = $this->travelReport->find($id);
        // $this->authorize('print', $travelReport);
        $travelReport = $this->travelReport->select('*')
            ->with('status')
            ->where('id', $id)
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->first();
        $approver = $this->employees->select('*')->where('id', $travelReport->approver->employee_id)->first();
        $requester = $this->employees->select('*')->where('id', $travelReport->reporter->employee_id)->first();
        $date['submitted_date'] = '';
        $date['approved_date'] = '';
        foreach ($travelReport->logs as $log) {
            if ($log->status_id == 3) {
                $date['submitted_date'] = $log->created_at->format('Y-m-d');
            }
            if ($log->status_id == 6) {
                $date['approved_date'] = $date['recommended_date'] = $log->created_at->format('Y-m-d');
            }
        }

        $requesterSignature = null;
        if ($requester && $requester->signature && file_exists(public_path('storage/' . $requester->signature))) {
            $requesterSignature = asset('storage/' . $requester->signature);
        }

        $approverSignature = null;
        if ($approver && $approver->signature && file_exists(public_path('storage/' . $approver->signature))) {
            $approverSignature = asset('storage/' . $approver->signature);
        }

        return view('TravelRequest::TravelReportApproved.print')
            ->withApprover($approver)
            ->withDates($date)
            ->withRequester($requester)
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest)
            ->withRequesterSignature($requesterSignature)
            ->withApproverSignature($approverSignature);
    }


    /**
     * Show the specified travel report.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $travelReport = $this->travelReport->find($id);
        // $this->authorize('print', $travelReport);
        return view('TravelRequest::TravelReportApproved.show')
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest);
    }

}
