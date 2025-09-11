<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $advanceRequests;
    private $employees;
    /**
     * Create a new controller instance.
     *
     * @param AdvanceRequestRepository $AdvanceRequests
     * @param EmployeeRepository $employees
     */
    public function __construct(
        AdvanceRequestRepository $advanceRequests,
        EmployeeRepository       $employees

    )
    {
        $this->advanceRequests = $advanceRequests;
        $this->employees = $employees;
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->advanceRequests->getApproved();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('advance_number', function ($row) {
                    return $row->getAdvanceRequestNumber();
                })->addColumn('project_code', function ($row) {
                return $row->getProjectCode();
            })
                ->addColumn('requester', function ($row) {
                    return $row->requester->getFullName();
                })
                ->addColumn('district', function ($row) {
                    return $row->district->getDistrictName();
                })
                ->addColumn('office', function ($row) {
                    return $row->office->getOfficeName();
                })
                ->addColumn('required_date', function ($row) {
                    return $row->getRequiredDate();
                })->addColumn('estimated_amount', function ($row) {
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.advance.requests.show', $row->id) . '" rel="tooltip" title="View Advance Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('advance.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';

                    if($authUser->can('close', $row)){
                        $btn .= '&emsp;<button class="btn btn-danger btn-sm close-advance-modal-form" href="';
                        $btn .= route('close.advance.requests.create', $row->id) . '" rel="tooltip" title="Close ADV"><i class="bi bi-x-circle"></i></button>';
                    }
                    if ($authUser->can('pay',$row)){
                        $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                        $btn .= route('approved.advance.pay.create', $row->id) . '" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                    }

                    return $btn;
                })->addColumn('attachment', function ($row) use ($authUser) {
                    $attachment = '';
                    if ($row->attachment) {
                        $attachment .= '<div class="media"><a href="' . asset('storage/' . $row->attachment) . '" target="_blank" class="fs-5" title="View Attachment">';
                        $attachment .= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                    }
                    return $attachment;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Approved.index');
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
        $advanceRequests = $this->advanceRequests->with(['fiscalYear', 'status', 'advanceRequestDetails'])->find($id);
        $this->authorize('print', $advanceRequests);

        $digit = \App\Helper::convertNumberToWords($advanceRequests->getEstimatedAmount());

        return view('AdvanceRequest::print')
            ->withAdvanceRequest($advanceRequests)
            ->withDigit($digit);
    }

    /**
     * Show the specified advance request.
     *
     * @param $advanceRequestId
     * @return mixed
     */
    public function show($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $this->authorize('viewApproved', $advanceRequest);
        return view('AdvanceRequest::Approved.show')
            ->withAdvanceRequest($advanceRequest);
    }
}
