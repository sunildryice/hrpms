<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Project\Models\TimeSheet;
use Modules\Project\Models\TimeSheetLog;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Notifications\TimeSheetSubmitted;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class MonthlyTimeSheetApprovedController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $user

    ) {
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
                $date['submitted_date'] = $log->created_at;
            }
            if ($log->status_id == 6) {
                $date['approved_date'] = $date['recommended_date'] = $log->created_at;
            }
        }

        return view('TravelRequest::TravelReportApproved.print')
            ->withApprover($approver)
            ->withDates($date)
            ->withRequester($requester)
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest);
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
