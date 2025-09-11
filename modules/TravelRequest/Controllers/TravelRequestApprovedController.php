<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
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
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Yajra\DataTables\DataTables;

class TravelRequestApprovedController extends Controller
{
    private $travelRequest;

    /**
     * Create a new controller instance.
     *
     * @param DsaCategoryRepository   $dsaCategory,
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
     */
    public function __construct(
        DsaCategoryRepository $dsaCategory,
        DepartmentRepository $departments,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYear,
        OfficeRepository $offices,
        ProjectCodeRepository $projectCodes,
        TravelModeRepository $travelModes,
        TravelRequestRepository $travelRequest,
        TravelRequestEstimateRepository $travelRequestEstimate,
        TravelRequestItineraryRepository $travelRequestItinerary,
        StatusRepository $status,
        TravelTypeRepository $travelTypes,
        RoleRepository $roles,
        UserRepository $user
    ) {
        $this->dsaCategory = $dsaCategory;
        $this->departments = $departments;
        $this->employees = $employees;
        $this->fiscalYear = $fiscalYear;
        $this->offices = $offices;
        $this->projectCodes = $projectCodes;
        $this->status = $status;
        $this->travelRequest = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->travelModes = $travelModes;
        $this->travelTypes = $travelTypes;
        $this->roles = $roles;
        $this->user = $user;
        $this->destinationPath = 'travelrequest';
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
            $data = $this->travelRequest->getApproved();

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
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('approved.travel.requests.show', $row->id) . '" rel="tooltip" title="View Travel Request">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                if ($authUser->can('print', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('travel.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                }
                return $btn;
            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelRequestApproved.index');
    }

    /**
     * Show the specified advance request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $travelRequest = $this->travelRequest->find($id);
        // $this->authorize('print', $travelRequest);

        return view('TravelRequest::TravelRequestApproved.print')
            ->withRequester($travelRequest->requester->employee)
            ->withTravelRequest($travelRequest);
    }

    /**
     * Show the specified travel request.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($id);
        // $this->authorize('print', $travelRequest);
        return view('TravelRequest::TravelRequestApproved.show')
            ->withRequester($travelRequest->requester->employee)
            ->withTravelRequest($travelRequest);
    }
}
