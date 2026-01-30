<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\TravelRequest\Requests\LocalTravel\StoreRequest;
use Modules\TravelRequest\Notifications\LocalTravelSubmitted;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Requests\LocalTravel\UpdateRequest;
use Modules\TravelRequest\Repositories\TravelRequestRepository;

class LocalTravelController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected EmployeeRepository $employees,
        protected AccountCodeRepository $accountCodes,
        protected DonorCodeRepository $donorCodes,
        protected FiscalYearRepository $fiscalYear,
        protected LocalTravelRepository $localTravels,
        protected OfficeRepository $offices,
        protected TravelRequestRepository $travelRequests,
        protected UserRepository $users,
        protected ProjectCodeRepository $projectCodes,
        protected ProjectRepository $projects,
    ) {
        $this->destinationPath = 'localTravel';
    }

    /**
     * Display a listing of the local travel reimbursements
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->localTravels->with(['travelRequest', 'status', 'logs', 'requester'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })
                // ->orWhereHas('logs', function ($q) use ($authUser) {
                //     $q->where('user_id', $authUser->id);
                //     $q->orWhere('original_user_id', $authUser->id);
                // })
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('travel_number', function ($row) {
                    return $row->getTravelRequestNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('local.travel.reimbursements.show', $row->id).'" rel="tooltip" title="View Local Travel Reimbursement"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('local.travel.reimbursements.edit', $row->id).'" rel="tooltip" title="Edit Local Travel Reimbursement"><i class="bi-pencil-square"></i></a>';
                    } elseif ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('local.travel.reimbursements.print', $row->id).'" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('local.travel.reimbursements.destroy', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::LocalTravel.index');
    }

    /**
     * Show the form for creating new local travel.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        // $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $travelRequests = $this->travelRequests->select(['id', 'prefix', 'travel_number', 'modification_number', 'fiscal_year_id'])
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->whereRequesterId(auth()->id())
            ->get();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('TravelRequest::LocalTravel.create')->with([
            'activityCodes' => ($activityCodes),
            'consultants' => $this->employees->getActiveMembers(auth()->user()?->employee_id),
            'donorCodes' => ($donorCodes),
            'travelRequests' => ($travelRequests),
            'projects' => ($projects),
        ]);
    }

    /**
     * Store a newly created local travel in storage.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['requester_id'] = $inputs['created_by'] = auth()->id();
        $inputs['employee_id'] = isset($inputs['employee_id']) ? $inputs['employee_id'] : auth()->user()?->employee_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['status_id'] = 1;
        $localTravel = $this->localTravels->create($inputs);
        if ($localTravel) {
            return redirect()->route('local.travel.reimbursements.edit', $localTravel->id)
                ->withSuccessMessage('Local travel reimbursement successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Local travel reimbursement can not be added.');
    }

    /**
     * Show the specified local travel.
     *
     * @return mixed
     */
    public function show($localTravelId)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravels->find($localTravelId);

        return view('TravelRequest::LocalTravel.show')
            ->withLocalTravel($localTravel);
    }

    /**
     * Show the form for editing the specified local travel .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        $localTravel = $this->localTravels->find($id);
        $this->authorize('update', $localTravel);
        $travelRequests = $this->travelRequests->select(['id', 'prefix', 'travel_number', 'modification_number', 'fiscal_year_id'])
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->whereRequesterId(auth()->id())
            ->get();
        $supervisors = $this->users->getSupervisors($authUser);
        $accountCodes = $localTravel->activityCode ? $localTravel->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        // $projectCodes = $this->projectCodes->getActiveProjectCodes();

        return view('TravelRequest::LocalTravel.edit')
            ->with([
                'activityCodes' => ($activityCodes),
                'accountCodes' => ($accountCodes),
                'supervisors' => ($supervisors),
                'consultants' => $this->employees->getActiveMembers($authUser?->employee_id),
                'donorCodes' => ($donorCodes),
                'travelRequests' => ($travelRequests),
                'localTravel' => ($localTravel),
                'projects' => ($projects),
            ]);
    }

    /**
     * Update the specified employee in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $localTravel = $this->localTravels->find($id);
        $this->authorize('update', $localTravel);
        $inputs = $request->validated();
        $inputs['office_id'] = $localTravel->requester->employee->office_id;
        $inputs['updated_by'] = auth()->id();
        $inputs['employee_id'] = isset($inputs['employee_id']) ? $inputs['employee_id'] : auth()->user()?->employee_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $localTravel = $this->localTravels->update($id, $inputs);
        if ($localTravel) {
            $message = 'Local travel reimbursement is successfully updated.';
            if ($localTravel->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Local travel reimbursement is successfully submitted.';
                $localTravel->approver->notify(new LocalTravelSubmitted($localTravel));
            }

            return redirect()->route('local.travel.reimbursements.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Local travel reimbursement can not be updated.');
    }

    /**
     * Remove the specified local travel from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $localTravel = $this->localTravels->find($id);
        $this->authorize('delete', $localTravel);
        $flag = $this->localTravels->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Local travel reimbursement is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Local travel reimbursement can not deleted.',
        ], 422);
    }
}
