<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ClosedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $advanceRequests;
    private $purchaseOrderItems;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param PurchaseOrderItemRepository $purchaseOrderItems
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        AdvanceRequestRepository $advanceRequests,
        PurchaseOrderItemRepository  $purchaseOrderItems,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->purchaseOrderItems = $purchaseOrderItems;
        $this->users = $users;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->advanceRequests->getClosed();

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
                $btn .= route('closed.advance.requests.show', $row->id) . '" rel="tooltip" title="View Advance Request">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('advance.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
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

        return view('AdvanceRequest::Closed.index');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('viewApproved', $advanceRequest);

        return view('AdvanceRequest::Closed.show')
            ->withAdvanceRequest($advanceRequest);
    }
}
