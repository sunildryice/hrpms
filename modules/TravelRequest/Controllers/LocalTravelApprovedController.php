<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Yajra\DataTables\DataTables;

class LocalTravelApprovedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $localTravels;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LocalTravelRepository $localTravels
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        LocalTravelRepository $localTravels,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->localTravels = $localTravels;
        $this->users = $users;
    }

    /**
     * Display a listing of the approved local travel reimbursements
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->localTravels->getApproved()->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('submitted_date', function ($row) {
                    return $row->getSubmittedDate();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('local.travel.reimbursements.show', $row->id) . '" rel="tooltip" title="View Local Travel Reimbursement">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('local.travel.reimbursements.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    if ($authUser->can('pay', $row)) {
                        $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                        $btn .= route('approved.local.travel.reimbursements.pay.create', $row->id) . '" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('TravelRequest::LocalTravel.Approved.index');
    }

    /**
     * Show the specified travel claim in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravels->find($id);
        $this->authorize('print', $localTravel);

        $requester = $localTravel->requester?->employee;
        $reviewer = $localTravel->reviewer?->employee;
        $approver = $localTravel->approver?->employee;

        $requesterSignature = null;
        if ($requester && $requester->signature && file_exists(public_path('storage/' . $requester->signature))) {
            $requesterSignature = asset('storage/' . $requester->signature);
        }

        $reviewerSignature = null;
        if ($reviewer && $reviewer->signature && file_exists(public_path('storage/' . $reviewer->signature))) {
            $reviewerSignature = asset('storage/' . $reviewer->signature);
        }

        $approverSignature = null;
        if ($approver && $approver->signature && file_exists(public_path('storage/' . $approver->signature))) {
            $approverSignature = asset('storage/' . $approver->signature);
        }

        return view('TravelRequest::LocalTravel.print')
            ->withLocalTravel($localTravel)
            ->withRequester($localTravel->requester->employee)
            ->withRequesterSignature($requesterSignature)
            ->withReviewerSignature($reviewerSignature)
            ->withApproverSignature($approverSignature);
    }
}
