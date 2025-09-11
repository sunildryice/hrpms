<?php

namespace Modules\TravelAuthorization\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Master\Repositories\DepartmentRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Master\Repositories\TravelTypeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationSubmitted;

use Modules\TravelAuthorization\Requests\StoreRequest;
use Modules\TravelAuthorization\Requests\UpdateRequest;

use DataTables;
use DB;

class TravelAuthorizationController extends Controller
{
    protected $destinationPath;

    public function __construct(
        protected DepartmentRepository $departments,
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYear,
        protected OfficeRepository $offices,
        protected ProjectCodeRepository $projectCodes,
        protected TravelAuthorizationRepository $travel,
        protected StatusRepository $status,
        protected TravelTypeRepository $travelTypes,
        protected UserRepository $user,
    ) {
        $this->destinationPath = 'travelAuthorization';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travel->with(['status', 'fiscalYear', 'submittedLog', 'officials'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->office->getOfficeName();
                }) ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('request_number', function ($row) {
                    return $row->getTravelAuthorizationNumber();
                })->addColumn('submitted_date', function ($row) {
                    return $row->submittedLog?->created_at->format('Y-m-d');
                })->addColumn('officials', function ($row) {
                    return implode(', ', $row->officials->pluck('name')->toArray());
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('ta.requests.view', $row->id) . '" rel="tooltip" title="View Travel Authorization Request"><i class="bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('ta.requests.edit', $row->id) . '" rel="tooltip" title="Edit Travel Authorization Request"><i class="bi-pencil-square"></i></a>';
                    } else {
                        if ($authUser->can('print', $row)) {
                            $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                            $btn .= route('ta.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                        }
                    }

                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('ta.requests.destroy', $row->id) . '"  rel="tooltip" title="Delete Travel Authorization Request">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    }
                    else if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-request"';
                        $btn .= 'data-href = "' . route('ta.requests.amend.store', $row->id) . '" data-number = "' . $row->getTravelAuthorizationNumber() . '"  title="Amend Travel Authorization Request">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelAuthorization::index');
    }

    // public function create()
    // {
    //     $authUser = auth()->user();
    //     $projectCodes = $this->projectCodes->getActiveProjectCodes();
    //     $accompanyingStaffs = $this->employees->getActiveEmployees();
    //     $substitutes = $accompanyingStaffs->reject(function ($staff, $key) use ($authUser) {
    //         return $staff->id == $authUser->employee_id;
    //     });

    //     return view('TravelAuthorization::create')
    //         ->withProjects($projectCodes)
    //         ->withSubstitutes($substitutes)
    //         ->withTravelTypes($this->travelTypes->get());
    // }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['status_id'] = 1;
        $travel = $this->travel->create($inputs);
        if ($travel) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel Authorization Request is successfully created.',
                'redirectUrl' => route('ta.requests.edit', $travel->id),
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel Authorization Request can not be created.',
        ], 422);
    }

    public function edit($id)
    {
        $travel = $this->travel->find($id);
        $this->authorize('update', $travel);
        $authUser = auth()->user();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        $accompanyingStaffs = $this->employees->getActiveEmployees();
        $substitutes = $accompanyingStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $supervisors = $this->user->getSupervisors($authUser);
        $offices = $this->offices->getActiveOffices();
        $returnRemarks = $travel->returnedLog?->log_remarks ?: '';

        return view('TravelAuthorization::edit')
            ->withAuthUser(auth()->user())
            ->withReturnRemarks($returnRemarks)
            ->withProjects($projectCodes)
            ->withSupervisors($supervisors)
            ->withSubstitutes($substitutes)
            ->withTravel($travel)
            ->withOffices($offices)
            ->withTravelTypes($this->travelTypes->get());
    }

    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $travel = $this->travel->find($id);
        $this->authorize('update', $travel);
        $inputs = $request->validated();
        $inputs['status_id'] = $travel->status_id;
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travel = $this->travel->update($id, $inputs);
        if ($travel) {
            $message = 'Travel Authorization request is successfully updated.';
            $route = redirect()->route('ta.requests.edit', $travel->id);

            if ($travel->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Travel Authorization request is successfully submitted.';
                $travel->approver->notify(new TravelAuthorizationSubmitted($travel));
                $route = redirect()->route('ta.requests.index');
            }
            return $route->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Travel Authorization Request can not be updated.');
    }

    public function view($id)
    {
        $travel = $this->travel->find($id);
        $this->authorize('view', $travel);

        return view('TravelAuthorization::view')
            ->withAuthUser(auth()->user())
            ->withTravel($travel);
    }

    public function destroy($id)
    {
        $travel = $this->travel->find($id);
        $this->authorize('delete', $travel);
        $flag = $this->travel->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel Authorization Request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel Authorization Request can not deleted.',
        ], 422);
    }

    public function amend($id)
    {
        $travel = $this->travel->find($id);
        $this->authorize('amend', $travel);

        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->travel->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel Authorization request is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel Authorization request can not amended.',
        ], 422);
    }

}
