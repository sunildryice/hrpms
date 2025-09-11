<?php

namespace Modules\EmployeeRequest\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\EmployeeRequest\Notifications\EmployeeRequestSubmitted;
use Modules\EmployeeRequest\Repositories\EmployeeRequestRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\EducationLevelRepository;
use Modules\Master\Repositories\EmployeeTypeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeRequest\Requests\StoreRequest;
use Modules\EmployeeRequest\Requests\UpdateRequest;

use DataTables;
use Modules\EmployeeRequest\Notifications\EmployeeRequestRecommended;

class EmployeeRequestController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param DistrictRepository $districts
     * @param DonorCodeRepository $donorCodes
     * @param EducationLevelRepository $educationLevels
     * @param EmployeeTypeRepository $employeeTypes
     * @param FiscalYearRepository $fiscalYears
     * @param EmployeeRequestRepository $employeeRequests
     */
    public function __construct(
        protected ActivityCodeRepository    $activityCodes,
        protected DistrictRepository        $districts,
        protected DonorCodeRepository       $donorCodes,
        protected EducationLevelRepository  $educationLevels,
        protected EmployeeTypeRepository    $employeeTypes,
        protected FiscalYearRepository      $fiscalYears,
        protected EmployeeRequestRepository $employeeRequests,
        protected UserRepository            $users
    )
    {
        $this->destinationPath = 'employeeRequest';
    }

    /**
     * Display a listing of the employee requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->employeeRequests->with(['fiscalYear', 'status', 'dutyStation'])->select(['*'])
                ->whereCreatedBy($authUser->id)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('duty_station', function ($row) {
                    return $row->getDutyStation();
                })->addColumn('type', function ($row) {
                    return $row->getEmployeeType();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('employee.requests.show', $row->id) . '" rel="tooltip" title="View Employee Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('employee.requests.edit', $row->id) . '" rel="tooltip" title="Edit Employee Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.employee.requests.print', $row->id) . '" target="_blank" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('employee.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('EmployeeRequest::index');
    }

    /**
     * Show the form for creating a new employee request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $reviewers = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-employee-requisition');

        return view('EmployeeRequest::create')
            ->withActivityCodes($activityCodes)
            ->withDistricts($this->districts->getDistricts())
            ->withDonorCodes($donorCodes)
            ->withEducationLevels($this->educationLevels->get())
            ->withEmployeeTypes($this->employeeTypes->get())
            ->withFiscalYears($this->fiscalYears->get())
            ->withReviewers($reviewers)
            ->withApprovers($approvers);
    }

    /**
     * Store a newly created employee request in storage.
     *
     * @param \Modules\EmployeeRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $employee_id = auth()->user()->employee_id;
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee_id, time() . '_tor_JD.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['tor_jd_attachment'] = $filename;
        }

        $employeeRequest = $this->employeeRequests->create($inputs);

        if ($employeeRequest) {
            $message = 'Employee request is successfully added.';
            if ($employeeRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Employee request is successfully submitted.';
                if ($inputs['reviewer_id'] != null) {
                    $employeeRequest->reviewer->notify(new EmployeeRequestSubmitted($employeeRequest));
                } else {
                    $employeeRequest->approver->notify(new EmployeeRequestRecommended($employeeRequest));
                }
            }
            return redirect()->route('employee.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee request can not be added.');
    }

    /**
     * Show the specified employee request.
     *
     * @param $employeeRequestId
     * @return mixed
     */
    public function show($employeeRequestId)
    {
        $authUser = auth()->user();
        $employeeRequest = $this->employeeRequests->find($employeeRequestId);

        return view('EmployeeRequest::show')
            ->withEmployeeRequest($employeeRequest);
    }

    /**
     * Show the form for editing the specified employee request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $employeeRequest = $this->employeeRequests->find($id);
        $this->authorize('update', $employeeRequest);
        $attachment = '';
        if ($employeeRequest->tor_jd_attachment != NULL) {
            $attachment = asset('storage/' . $employeeRequest->tor_jd_attachment);
        }
        $reviewers = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-recommended-employee-requisition');
        $accountCodes = $employeeRequest->activityCode ? $employeeRequest->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('EmployeeRequest::edit')
            ->withAuthUser($authUser)
            ->withAttachment($attachment)
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDistricts($this->districts->getDistricts())
            ->withDonorCodes($donorCodes)
            ->withEducationLevels($this->educationLevels->get())
            ->withEmployeeRequest($employeeRequest)
            ->withEmployeeTypes($this->employeeTypes->get())
            ->withFiscalYears($this->fiscalYears->get())
            ->withReviewers($reviewers)
            ->withApprovers($approvers);
    }

    /**
     * Update the specified employee request in storage.
     *
     * @param \Modules\EmployeeRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $employeeRequest = $this->employeeRequests->find($id);
        $this->authorize('update', $employeeRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $employee_id = auth()->user()->employee_id;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee_id, time() . '_tor_JD.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['tor_jd_attachment'] = $filename;
        }
        $employeeRequest = $this->employeeRequests->update($id, $inputs);

        if ($employeeRequest) {
            $message = 'Employee request is successfully updated.';
            if ($employeeRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Employee request is successfully submitted.';
                if ($inputs['reviewer_id'] != null) {
                    $employeeRequest->reviewer->notify(new EmployeeRequestSubmitted($employeeRequest));
                } else {
                    $employeeRequest->approver->notify(new EmployeeRequestRecommended($employeeRequest));
                }
            }
            return redirect()->route('employee.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee Request can not be updated.');
    }

    /**
     * Remove the specified employee request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $employeeRequest = $this->employeeRequests->find($id);
        $this->authorize('delete', $employeeRequest);
        $flag = $this->employeeRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Employee request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Employee request can not deleted.',
        ], 422);
    }
}
