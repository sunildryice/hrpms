<?php

namespace Modules\TransportationBill\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TransportationBill\Notifications\TransportationBillApproved;
use Modules\TransportationBill\Notifications\TransportationBillRejected;
use Modules\TransportationBill\Notifications\TransportationBillReturned;
use Modules\TransportationBill\Notifications\TransportationBillSubmitted;
use Modules\TransportationBill\Repositories\TransportationBillRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TransportationBill\Requests\Approve\StoreRequest;
use DataTables;


class ApproveController extends Controller
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
            $data = $this->transportationBills->with(['fiscalYear', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })
                ->orderBy('bill_date', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bill_date', function ($row){
                    return $row->getBillDate();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.transportation.bills.create', $row->id) . '" rel="tooltip" title="Approve Transportation Bill">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TransportationBill::Approve.index');
    }

    public function create($transportationBillId)
    {
        $authUser = auth()->user();
        $transportationBill = $this->transportationBills->find($transportationBillId);
        $this->authorize('approve', $transportationBill);

        $latestTenure = $transportationBill->requester->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name'])
            ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        return view('TransportationBill::Approve.create')
            ->withAuthUser($authUser)
            ->withTransportationBill($transportationBill)
            ->withSupervisors($supervisors);
    }

    public function store(StoreRequest $request, $transportationBillId)
    {
        $transportationBill = $this->transportationBills->find($transportationBillId);
        $this->authorize('approve', $transportationBill);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $transportationBill = $this->transportationBills->approve($transportationBill->id, $inputs);

        if ($transportationBill) {
            $message = '';
            if ($transportationBill->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Transportation bill is successfully returned.';
                $transportationBill->requester->notify(new TransportationBillReturned($transportationBill));
            } else if ($transportationBill->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Transportation bill is successfully rejected.';
                $transportationBill->requester->notify(new TransportationBillRejected($transportationBill));
            } else if ($transportationBill->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Transportation bill is successfully recommended.';
                $transportationBill->approver->notify(new TransportationBillSubmitted($transportationBill));
            } else {
                $message = 'Transportation bill is successfully approved.';
                $transportationBill->requester->notify(new TransportationBillApproved($transportationBill));
            }

            return redirect()->route('approve.transportation.bills.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Transportation bill can not be approved.');
    }
}
