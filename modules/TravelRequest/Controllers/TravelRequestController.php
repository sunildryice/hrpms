<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Master\Repositories\TravelTypeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Notifications\TravelAdvanceRequestSubmitted;
use Modules\TravelRequest\Notifications\TravelRequestCancelSubmitted;
use Modules\TravelRequest\Notifications\TravelRequestSubmitted;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\Advance\StoreRequest as AdvanceStoreRequest;
use Modules\TravelRequest\Requests\Cancel\StoreRequest as CancelStoreRequest;
use Modules\TravelRequest\Requests\StoreRequest;
use Modules\TravelRequest\Requests\UpdateRequest;

class TravelRequestController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param  TravelRequestRepository  $travelRequest
     */
    public function __construct(
        protected DepartmentRepository $departments,
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYear,
        protected OfficeRepository $offices,
        protected ProjectCodeRepository $projectCodes,
        protected TravelRequestRepository $travelRequests,
        protected TravelRequestEstimateRepository $travelRequestEstimate,
        protected TravelRequestItineraryRepository $travelRequestItinerary,
        protected StatusRepository $status,
        protected TravelTypeRepository $travelTypes,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelRequests->with(['requester', 'logs'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })
                // ->orWhereHas('logs', function ($q) use ($authUser) {
                //     $q->where('user_id', $authUser->id);
                //     $q->orWhere('original_user_id', $authUser->id);
                // })
                ->orderBy('departure_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->getReturnDate();
                })->addColumn('duration', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('requester', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    $advanceStatus = '';
                    if ($row->advance_received_at) {
                        $advanceStatus = '<br><span class="badge bg-success">Advance Received</span>';
                    } elseif ($row->advance_requested_at) {
                        $advanceStatus = '<br><span class="badge bg-warning">Advance Requested</span>';
                    }

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>' . $advanceStatus;
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('travel.requests.view', $row->id) . '" rel="tooltip" title="View Travel Request"><i class="bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('travel.requests.edit', $row->id) . '" rel="tooltip" title="Edit Travel Request"><i class="bi-pencil-square"></i></a>';
                    } else {
                        if ($authUser->can('print', $row)) {
                            $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                            $btn .= route('travel.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                        }
                        if ($authUser->can('cancel', $row)) {
                            $btn .= '&emsp;<button class="btn btn-danger btn-sm cancel-travel-request" href="';
                            $btn .= route('travel.requests.cancel.create', $row->id) . '" rel="tooltip" title="Cancel"><i class="bi bi-x-circle"></i></button>';
                        }
                    }

                    if ($authUser->can('createReport', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('travel.reports.create', $row->id) . '" rel="tooltip" title="Create Travel Report"><i class="bi-list-columns-reverse"></i></a>';
                    }

                    if ($authUser->can('createClaim', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm create-settlement" data-href="';
                        $btn .= route('travel.claims.store', $row->id) . '" rel="tooltip" title="Create Travel Settlement"><i class="bi-bank2"></i></a>';
                    }

                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.requests.destroy', $row->id) . '"  rel="tooltip" title="Delete Travel Request">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    } elseif ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-travel-request"';
                        $btn .= 'data-href = "' . route('travel.requests.amend.store', $row->id) . '" title="Amend Travel Request">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    // if ($authUser->can('askAdvance', $row)) {
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-outline-warning btn-sm travel-advance-request" data-travel-request-id="' . $row->id . '"';
                    //     $btn .= 'data-href = "' . route('travel.requests.advance.store', $row->id) . '" data-travel-number="' . $row->getTravelRequestNumber() . '" title="Travel Request Advance">';
                    //     $btn .= '<i class="bi bi-cash" ></i></a>';
                    // }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $employee = $authUser->employee;
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $accompanyingStaffs = $this->employees->getActiveEmployees();
        $substitutes = $accompanyingStaffs->reject(function ($staff) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $consultants = $this->employees->getActiveConsultants();

        return view('TravelRequest::create')
            ->withProjects($projectCodes)
            ->withSubstitutes($substitutes)
            ->with('consultants', $consultants)
            ->withTravelTypes($this->travelTypes->get())
            ->with('employee', $employee)
            ->with('employeePassportNumber', $employee->passport_number)
            ->with('employeePassportAttachment', $employee->passport_attachment);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $checkExists = $this->travelRequests->select(['*'])
            // ->where('requester_id', $authUser->id)
            ->where(function ($q) use ($authUser) {
                $q->where('requester_id', $authUser->id)
                    ->orWhere('employee_id', $authUser->employee_id);
            })
            ->whereNotIn('status_id', [config('constant.REJECTED_STATUS'), config('constant.AMENDED_STATUS'), config('constant.CANCELLED_STATUS')])
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '<=', $inputs['departure_date'])
                        ->whereDate('return_date', '>=', $inputs['departure_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '<=', $inputs['return_date'])
                        ->whereDate('return_date', '>=', $inputs['return_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '>', $inputs['departure_date'])
                        ->whereDate('return_date', '<', $inputs['return_date']);
                });
            })->first();

        if ($checkExists) {
            return redirect()->back()->withInput()
                ->withWarningMessage('Travel request overlaps for selected date range.');
        }

        $authUser = auth()->user();

        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['employee_id'] = isset($inputs['employee_id']) ? $inputs['employee_id'] : $authUser->employee_id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['status_id'] = 1;
        $externalTravelers = $request->input('external_travelers', []);

        $inputs['external_travelers'] = is_array($externalTravelers) ? $externalTravelers : [];
        $inputs['external_traveler_count'] = count(
            array_filter($inputs['external_travelers'], fn($t) => !empty($t['name'] ?? null))
        );
        $travelRequest = $this->travelRequests->create($inputs);
        if ($travelRequest) {
            return redirect()->route('travel.requests.edit', $travelRequest->id)
                ->withSuccessMessage('Travel Request successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Travel Request can not be added.');
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $travelRequest = $this->travelRequests->find($id);
        $this->authorize('update', $travelRequest);
        $authUser = auth()->user();
        $employee = $authUser->employee;
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $accompanyingStaffs = $this->employees->getActiveEmployees();
        $substitutes = $accompanyingStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $supervisors = $this->user->getSupervisors($authUser);

        return view('TravelRequest::edit')
            ->withAuthUser(auth()->user())
            ->withProjects($projectCodes)
            ->withSupervisors($supervisors)
            ->withSubstitutes($substitutes)
            ->withTravelRequest($travelRequest)
            ->withTravelRequest($travelRequest)
            ->with('consultants', $this->employees->getActiveConsultants())
            ->withTravelTypes($this->travelTypes->get())
            ->with('employee', $employee)
            ->with('employeePassportNumber', $employee->passport_number)
            ->with('employeePassportAttachment', $employee->passport_attachment);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Modules\Employee\Requests\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequests->find($id);
        $this->authorize('update', $travelRequest);
        $inputs = $request->validated();

        $checkExists = $this->travelRequests->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('requester_id', $authUser->id)
                    ->orWhere('employee_id', $authUser->employee_id);
            })
            ->whereNotIn('id', [$travelRequest->id])
            ->whereNotIn('status_id', [config('constant.REJECTED_STATUS'), config('constant.AMENDED_STATUS'), config('constant.CANCELLED_STATUS')])
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '<', $inputs['departure_date'])
                        ->whereDate('return_date', '>', $inputs['departure_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '<', $inputs['return_date'])
                        ->whereDate('return_date', '>', $inputs['return_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('departure_date', '>', $inputs['departure_date'])
                        ->whereDate('return_date', '<', $inputs['return_date']);
                });
            })->first();

        if ($checkExists) {
            return redirect()->back()->withInput()
                ->withWarningMessage('Travel request overlaps for selected date range.');
        }

        $inputs['employee_id'] = isset($inputs['employee_id']) ? $inputs['employee_id'] : $authUser->employee_id;
        $inputs['status_id'] = $travelRequest->status_id;
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $externalTravelers = $request->input('external_travelers', []);

        $inputs['external_travelers'] = is_array($externalTravelers) ? $externalTravelers : [];
        $inputs['external_traveler_count'] = count(
            array_filter($inputs['external_travelers'], fn($t) => !empty($t['name'] ?? null))
        );
        $travelRequest = $this->travelRequests->update($id, $inputs);
        if ($travelRequest) {
            $message = 'Travel request is successfully updated.';
            $route = redirect()->route('travel.requests.edit', $travelRequest->id);

            if ($travelRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Travel request is successfully submitted.';
                $travelRequest->approver->notify(new TravelRequestSubmitted($travelRequest));
                $route = redirect()->route('travel.requests.index');
            }

            return $route->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Travel Request can not be updated.');
    }

    /**
     * View the details the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id)
    {
        $travelRequest = $this->travelRequests->find($id);
        //        $this->authorize('view', $travelRequest);

        return view('TravelRequest::view')
            ->withAuthUser(auth()->user())
            ->withTravelRequest($travelRequest);
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $travelRequest = $this->travelRequests->find($id);
        $this->authorize('delete', $travelRequest);
        $flag = $this->travelRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel Request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel Request can not deleted.',
        ], 422);
    }

    /**
     * Amend the specified leave request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $travelRequest = $this->travelRequests->find($id);
        $this->authorize('amend', $travelRequest);

        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->travelRequests->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel request is successfully amended.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel request can not amended.',
        ], 422);
    }

    public function cancelCreate($id)
    {
        $travelRequest = $this->travelRequests->find($id);

        // $this->authorize('cancel', $travelRequest);
        return view('TravelRequest::TravelRequest.cancel.create')->with('travelRequest', $travelRequest);
    }

    /**
     * Cancel the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(CancelStoreRequest $request, $id)
    {
        $travelRequest = $this->travelRequests->find($id);
        $this->authorize('cancel', $travelRequest);

        if (!isset($travelRequest->approver_id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Travel Request approver not set.',
            ], 422);
        }

        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['status_id'] = config('constant.INIT_CANCEL_STATUS');
        $inputs['log_remarks'] = 'Travel request cancel is submitted.';
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['updated_by'] = auth()->id();

        $travelRequest = $this->travelRequests->cancel($id, $inputs);
        if ($travelRequest) {
            if ($travelRequest->status_id == config('constant.INIT_CANCEL_STATUS')) {
                if ($travelRequest->approver_id) {
                    $travelRequest->approver->notify(new TravelRequestCancelSubmitted($travelRequest));
                }
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Travel Request cancellation is submitted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel Request can not be cancelled.',
        ], 422);
    }

    public function advance(AdvanceStoreRequest $request, $travelId)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequests->find($travelId);
        $this->authorize('askAdvance', $travelRequest);
        $inputs['advance_requested_at'] = date('Y-m-d H:i:s');
        $travelRequest = $this->travelRequests->storeAdvance($travelRequest->id, $inputs);
        if ($travelRequest) {
            foreach ($this->user->permissionBasedUsers('travel-request-advance') as $financeUsers) {
                $financeUsers->notify(new TravelAdvanceRequestSubmitted($travelRequest));
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Travel Advance is requested successfully.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel Advance can not be requested.',
        ], 422);
    }
}
