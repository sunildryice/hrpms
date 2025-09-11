<?php

namespace Modules\FundRequest\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Notifications\FundRequestCancelSubmitted;
use Modules\FundRequest\Notifications\FundRequestSubmitted;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\FundRequest\Requests\StoreRequest;
use Modules\FundRequest\Requests\UpdateRequest;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

class FundRequestController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected FundRequestRepository $fundRequests,
        protected Helper $helper,
        protected OfficeRepository $offices,
        protected ProjectCodeRepository $projectCodes,
        protected UserRepository $users
    ) {
        $this->destinationPath = 'fundRequest';
    }

    /**
     * Display a listing of the fund requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->fundRequests->with(['fiscalYear', 'status', 'projectCode', 'district'])
                ->where(function ($q) use ($authUser) {
                    $q->whereCreatedBy($authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->getOfficeName();
                })->addColumn('request_for_office', function ($row) {
                    return $row->getRequestForOfficeName();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('year', function ($row) {
                    return $row->getFiscalYear();
                })->addColumn('month', function ($row) {
                    return $row->getMonthName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('fund_number', function ($row) {
                    return $row->getFundRequestNumber();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('fund.requests.show', $row->id).'" rel="tooltip" title="View Fund Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('fund.requests.edit', $row->id).'" rel="tooltip" title="Edit Fund Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('fund.requests.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('approved.fund.requests.print', $row->id).'" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }

                    if ($authUser->can('cancel', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-outline-danger cancel-record"';
                        $btn .= 'data-href = "'.route('fund.requests.cancel', $row->id).'" data-number="'.$row->getFundRequestNumber().'" title="Revoke Fund Request">';
                        $btn .= '<i class="bi bi-x-lg" ></i></a>';
                    }

                    if ($authUser->can('replicate', $row)) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-outline-primary btn-sm replicate-record" rel="tooltip" title="Replicate Fund Request"';
                        $btn .= 'data-href="'.route('fund.requests.replicate.store', $row->id).'"';
                        $btn .= ' data-description="'.$row->getDescription().'">';
                        $btn .= '<i class="bi bi-files"></i></a>';
                    }

                    if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-fund-request"';
                        $btn .= 'data-href = "'.route('fund.requests.amend.store', $row->id).'" title="Amend Fund Request">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('FundRequest::index');
    }

    /**
     * Show the form for creating a new fund request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $districts = $this->districts->getEnabledDistricts();

        return view('FundRequest::create')
            ->with([
                'districts' => ($districts),
                'fiscalYears' => ($this->fiscalYears->get()),
                'months' => ($this->helper->getMonthArray()),
                'offices' => ($this->offices->getActiveOffices()),
                'projectCodes' => ($projectCodes),
            ]);
    }

    /**
     * Store a newly created fund request in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $timestamp = strtotime($inputs['year_month']);
        $inputs['year'] = date('Y', $timestamp);
        $inputs['month'] = date('m', $timestamp);
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $date = date('Y-m-d', $timestamp);
        $fiscalYear = $this->fiscalYears->select('*')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
        $inputs['fiscal_year_id'] = $fiscalYear->id;

        $fundRequest = $this->fundRequests->create($inputs);

        if ($fundRequest) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath.'/'.$fundRequest->id, time().'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
                $fundRequest->update(['attachment' => $filename]);
            }

            return redirect()->route('fund.requests.edit', $fundRequest->id)
                ->withSuccessMessage('Fund Request successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Fund Request can not be added.');
    }

    /**
     * Show the specified fund request.
     *
     * @return mixed
     */
    public function show($fundRequestId)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($fundRequestId);

        return view('FundRequest::show')
            ->with('fundRequest', $fundRequest);
    }

    /**
     * Show the form for editing the specified fund request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($id);
        $this->authorize('update', $fundRequest);

        $supervisors = $this->users->getSupervisors($authUser);
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $districts = $this->districts->getEnabledDistricts();

        $reviewers = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-fund-request');

        return view('FundRequest::edit')
            ->with([
                'authUser' => ($authUser),
                'districts' => ($districts),
                'fiscalYears' => ($this->fiscalYears->get()),
                'fundRequest' => ($fundRequest),
                'months' => ($this->helper->getMonthArray()),
                'offices' => ($this->offices->getActiveOffices()),
                'projectCodes' => ($projectCodes),
                'supervisors' => ($supervisors),
                'reviewers' => ($reviewers),
                'approvers' => ($approvers),
            ]);
    }

    /**
     * Update the specified fund request in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $fundRequest = $this->fundRequests->find($id);
        $this->authorize('update', $fundRequest);
        $inputs = $request->validated();
        $timestamp = strtotime($inputs['year_month']);
        $inputs['year'] = date('Y', $timestamp);
        $inputs['month'] = date('m', $timestamp);
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $date = date('Y-m-d', $timestamp);
        $fiscalYear = $this->fiscalYears->select('*')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath.'/'.$fundRequest->id, time().'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $fundRequest = $this->fundRequests->update($id, $inputs);

        if ($fundRequest) {
            $message = 'Fund request is successfully updated.';
            if ($fundRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Fund request is successfully submitted.';
                $fundRequest->checker->notify(new FundRequestSubmitted($fundRequest));
            }

            return redirect()->route('fund.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Fund Request can not be updated.');
    }

    /**
     * Remove the specified fund request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $fundRequest = $this->fundRequests->find($id);
        $this->authorize('delete', $fundRequest);
        $flag = $this->fundRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Fund request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Fund request can not deleted.',
        ], 422);
    }

    public function printApprovedFundRequest($fundRequestId)
    {
        $fundRequest = $this->fundRequests->find($fundRequestId);
        // $this->authorize('viewApproved', $fundRequest);

        return view('FundRequest::Approved.print', compact('fundRequest'));
    }

    public function cancel(Request $request, $poId)
    {
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);

        $fundRequest = $this->fundRequests->find($poId);
        $this->authorize('cancel', $fundRequest);
        $fundRequest = $this->fundRequests->requestCancel($poId, $inputs);

        if ($fundRequest) {
            if ($fundRequest->status_id == config('constant.INIT_CANCEL_STATUS')) {
                $fundRequest->approver->notify(new FundRequestCancelSubmitted($fundRequest));

                return response()->json(['status' => 'success', 'message' => 'Fund Request cancel requested successfully'], 200);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to request Fund Request cancellation'], 422);
    }

    public function amend($id)
    {
        $fundRequest = $this->fundRequests->find($id);
        $this->authorize('amend', $fundRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $clone = $this->fundRequests->amend($id, $inputs);
        if ($clone) {
            return response()->json([
                'type' => 'success',
                'message' => 'Fund request is successfully amended.',
                'redirectUrl' => route('fund.requests.edit', $clone->id),
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Fund request can not be amended.',
        ], 422);
    }

    public function replicate($id)
    {
        $fund = $this->fundRequests->find($id);
        $this->authorize('replicate', $fund);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->fundRequests->replicate($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Fund request is successfully replicated.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Fund request can not replicated.',
        ], 422);
    }
}
