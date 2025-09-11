<?php

namespace Modules\TransportationBill\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TransportationBill\Repositories\TransportationBillRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;


class ApprovedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param TransportationBillRepository $transportationBills
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        TransportationBillRepository $transportationBills,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->transportationBills = $transportationBills;
        $this->users = $users;
    }

    /**
     * Display a listing of the transportation bills
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->transportationBills->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bill_date', function ($row){
                    return $row->getBillDate();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.transportation.bills.show', $row->id) . '" rel="tooltip" title="View Transportation Bill">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('transportation.bills.print', $row->id) . '" rel="tooltip" title="Print Transportation Bill"><i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('TransportationBill::Approved.index');
    }

    /**
     * Show the specified transportation bill.
     *
     * @param $transportationRequestId
     * @return mixed
     */
    public function show($transportationRequestId)
    {
        $transportationRequest = $this->transportationBills->find($transportationRequestId);
        $this->authorize('viewApproved', $transportationRequest);

        return view('TransportationBill::Approved.show')
            ->withTransportationBill($transportationRequest);
    }
}
