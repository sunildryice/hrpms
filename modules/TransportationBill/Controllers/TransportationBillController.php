<?php

namespace Modules\TransportationBill\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\TransportationBill\Notifications\TransportationBillSubmitted;
use Modules\TransportationBill\Repositories\TransportationBillRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TransportationBill\Requests\StoreRequest;
use Modules\TransportationBill\Requests\UpdateRequest;

use DataTables;

class TransportationBillController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param TransportationBillRepository $transportationBills
     * @param ProjectCodeRepository $projectCodes
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository $districts,
        EmployeeRepository     $employees,
        FiscalYearRepository   $fiscalYears,
        TransportationBillRepository $transportationBills,
        Helper $helper,
        ProjectCodeRepository $projectCodes,
        UserRepository         $users
    )
    {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->transportationBills = $transportationBills;
        $this->helper = $helper;
        $this->projectCodes = $projectCodes;
        $this->users = $users;
        $this->destinationPath = 'transportationBill';
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
            $data = $this->transportationBills->with(['fiscalYear', 'status', 'office', 'logs'])
                ->whereCreatedBy($authUser->id)
                ->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
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
                    $btn .= route('transportation.bills.show', $row->id) . '" rel="tooltip" title="View Transportation Bill"><i class="bi bi-eye"></i></a>';
                    if($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('transportation.bills.edit', $row->id) . '" rel="tooltip" title="Edit Transportation Bill"><i class="bi-pencil-square"></i></a>';
                    } else {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('transportation.bills.print', $row->id) . '" rel="tooltip" title="Print Transportation Bill"><i class="bi bi-printer"></i></a>';
                    }
                    if($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('transportation.bills.destroy', $row->id) . 'rel="tooltip" title="Delete Transportation Bill"">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TransportationBill::index');
    }

    /**
     * Show the form for creating a new transportation bill by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        return view('TransportationBill::create')
            ->withDistricts($this->districts->get())
            ->withFiscalYears($this->fiscalYears->get())
            ->withMonths($this->helper->getMonthArray())
            ->withProjectCodes($this->projectCodes->select(['*'])->whereNotNull('activated_at')->get());
    }

    /**
     * Store a newly created transportation bill in storage.
     *
     * @param \Modules\TransportationBill\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $transportationBill = $this->transportationBills->create($inputs);

        if ($transportationBill) {
            return redirect()->route('transportation.bills.edit', $transportationBill->id)
                ->withSuccessMessage('Transportation bill successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Transportation bill can not be added.');
    }

    /**
     * Show the specified transportation bill.
     *
     * @param $transportationBillId
     * @return mixed
     */
    public function show($transportationBillId)
    {
        $authUser = auth()->user();
        $transportationBill = $this->transportationBills->find($transportationBillId);

        return view('TransportationBill::show')
            ->withTransportationBill($transportationBill);
    }

    /**
     * Show the form for editing the specified transportation bill.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $transportationBill = $this->transportationBills->find($id);
        $this->authorize('update', $transportationBill);
        $receivers = $this->users->getActiveUsers();

        return view('TransportationBill::edit')
            ->withAuthUser($authUser)
            ->withDistricts($this->districts->get())
            ->withFiscalYears($this->fiscalYears->get())
            ->withTransportationBill($transportationBill)
            ->withMonths($this->helper->getMonthArray())
            ->withProjectCodes($this->projectCodes->getActiveProjectCodes())
            ->withReceivers($receivers);
    }

    /**
     * Update the specified transportation bill in storage.
     *
     * @param \Modules\TransportationBill\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $transportationBill = $this->transportationBills->find($id);
        $this->authorize('update', $transportationBill);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $transportationBill = $this->transportationBills->update($id, $inputs);

        if ($transportationBill) {
            $message = 'Transportation bill is successfully updated.';
            if ($transportationBill->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Transportation bill is successfully submitted.';
                $transportationBill->receiver->notify(new TransportationBillSubmitted($transportationBill));
//                if($transportationBill->alternateReceiver){
//                    $transportationBill->alternateReceiver->notify(new TransportationBillSubmitted($transportationBill));
//                }
            }
            return redirect()->route('transportation.bills.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Transportation bill can not be updated.');
    }

    /**
     * Remove the specified transportation bill from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $transportationBill = $this->transportationBills->find($id);
        $this->authorize('delete', $transportationBill);
        $flag = $this->transportationBills->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Transportation bill is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Transportation bill can not deleted.',
        ], 422);
    }

    /**
     * Show the specified transportation bill in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printBill($id)
    {
        $authUser = auth()->user();
        $transportationBill = $this->transportationBills->find($id);

        return view('TransportationBill::print')
            ->withTransportationBill($transportationBill);
    }
}
