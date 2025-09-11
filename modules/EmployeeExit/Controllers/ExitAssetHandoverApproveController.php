<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\ExitAssetHandover\ExitAssetHandoverApproved;
use Modules\EmployeeExit\Notifications\ExitAssetHandover\ExitAssetHandoverRecommended;
use Modules\EmployeeExit\Notifications\ExitAssetHandover\ExitAssetHandoverReturned;
use Modules\EmployeeExit\Repositories\ExitAssetHandoverRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeExit\Requests\ExitAssetHandover\Approve\StoreRequest;
use DataTables;

class ExitAssetHandoverApproveController extends Controller
{

   protected $exitAssetHandovers;
    public function __construct(
        EmployeeRepository             $employees,
        ExitHandOverNoteRepository     $exitHandOverNote,
        ExitAssetHandoverRepository    $exitAssetHandovers,
        UserRepository                 $users,
        GoodRequestAssetRepository     $goodRequestAssets
    )
    {
        $this->employees = $employees;
        $this->exitHandOverNote = $exitHandOverNote;
        $this->exitAssetHandovers = $exitAssetHandovers;
        $this->users = $users;
        $this->goodRequestAssets = $goodRequestAssets;
        $this->destinationPath = 'employeeExit';
    }
    /**
     * Display a listing of the exit asset handover.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->exitAssetHandovers->with(['employee', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row){
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row){
                    return $row->exitHandOverNote->getLastDutyDate();
                })->addColumn('resignation_date', function ($row){
                    return $row->exitHandOverNote->getResignationDate();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    // if($authUser->can('approve', $row)) {
                        $btn = '<a href = "'.route('approve.exit.handover.asset.create', $row->id).'" class="btn btn-secondary btn-sm"  rel="tooltip" title="Approve Asset Handover">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    // }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitAssetHandOver.Approve.index');

    }

    /**
     * Show the form for creating a new approve asset handover form.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    /** */
    public function create($id){
        $authUser = auth()->user();
        $exitAssetHandovers = $this->exitAssetHandovers->find($id);
        $this->authorize('approve', $exitAssetHandovers);
        $assets = $this->goodRequestAssets->with(['asset','submittedLog'])
        ->where('assigned_user_id', $exitAssetHandovers->employee->getUserId())
        ->get();
        $approvers = $this->users->permissionBasedUsers('approve-exit-interview');
        return view('EmployeeExit::ExitAssetHandOver.Approve.create')
            ->withAuthUser(auth()->user())
            ->withApprovers($approvers)
            ->withAssets($assets)
            ->withExitAssetHandover($exitAssetHandovers);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\EmployeeExit\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $exitAssetHandovers = $this->exitAssetHandovers->find($id);

        $this->authorize('approve', $exitAssetHandovers);

        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitAssetHandovers = $this->exitAssetHandovers->approve($exitAssetHandovers->id, $inputs);
        if ($exitAssetHandovers) {
            $message = '';
            if ($exitAssetHandovers->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Exit Asset Handover is successfully returned.';
                $exitAssetHandovers->employee->user->notify(new ExitAssetHandoverReturned($exitAssetHandovers));
            }else if ($exitAssetHandovers->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Exit Asset Handover is successfully recommended.';
                $exitAssetHandovers->approver->notify(new ExitAssetHandoverRecommended($exitAssetHandovers));
            } else {
                $message = 'Exit Asset Handover is successfully approved.';
                $exitAssetHandovers->employee->user->notify(new ExitAssetHandoverApproved($exitAssetHandovers));
            }
            return redirect()->route('approve.exit.handover.asset.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Exit Asset Handover can not be approved.');
    }
}
