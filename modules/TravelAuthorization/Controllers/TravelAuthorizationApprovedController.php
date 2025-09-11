<?php

namespace Modules\TravelAuthorization\Controllers;

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
use Modules\TravelAuthorization\Repositories\TravelAuthorizationEstimateRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationItineraryRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;
use Yajra\DataTables\DataTables;

class TravelAuthorizationApprovedController extends Controller
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
        protected TravelAuthorizationRepository $travel,
        protected TravelAuthorizationEstimateRepository $travelRequestEstimate,
        protected TravelAuthorizationItineraryRepository $travelRequestItinerary,
        protected StatusRepository $status,
        protected TravelTypeRepository $travelTypes,
        protected RoleRepository $roles,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'travelAuthorization';
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
            $data = $this->travel->with(['status', 'fiscalYear', 'submittedLog', 'officials'])
                        ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                        ->orderBy('request_date', 'desc')
                        ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('request_number', function ($row) {
                    return $row->getTravelAuthorizationNumber();
                })->addColumn('submitted_date', function ($row) {
                    return $row->submittedLog->created_at->format('Y-m-d');
                })->addColumn('officials', function ($row) {
                    return implode(', ', $row->officials->pluck('name')->toArray());
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.ta.requests.show', $row->id) . '" rel="tooltip" title="View Travel Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('ta.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelAuthorization::Approved.index');
    }

    /**
     * Show the specified advance request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $travel = $this->travel->find($id);
        $this->authorize('print', $travel);

        return view('TravelAuthorization::Approved.print')
            ->withRequester($travel->requester->employee)
            ->withTravel($travel);
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
        $travel = $this->travel->find($id);
        // $this->authorize('print', $travel);
        return view('TravelAuthorization::Approved.show')
            ->withRequester($travel->requester->employee)
            ->withTravel($travel);
    }
}
