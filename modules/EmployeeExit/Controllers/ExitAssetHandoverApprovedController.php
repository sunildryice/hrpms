<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Repositories\ExitAssetHandoverRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;

class ExitAssetHandoverApprovedController extends Controller
{

   protected $destinationPath;
    public function __construct(
        protected EmployeeRepository             $employees,
        protected ExitHandOverNoteRepository     $exitHandOverNote,
        protected ExitAssetHandoverRepository    $exitAssetHandovers,
        protected UserRepository                 $users,
        protected GoodRequestAssetRepository     $goodRequestAssets
    )
    {
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
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                })->orderBy('created_at', 'desc');

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
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.exit.handover.asset.show', $row->id) . '" rel="tooltip" title="View Asset Handover"><i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a href = "' . route('exit.employee.handover.asset.print', $row->id) . '" target="_blank" class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Asset Handover">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitAssetHandOver.Approved.index');

    }

    public function show(Request $request, $id)
    {
        $authUser = auth()->user();
        $exitAssetHandover = $this->exitAssetHandovers->find($id);

         $assets = $this->goodRequestAssets->with(['asset','submittedLog'])
        ->where('assigned_user_id', $exitAssetHandover->employee->getUserId())
        ->get();
        return view('EmployeeExit::ExitAssetHandOver.Approved.show')
            ->withAuthUser(auth()->user())
            ->withAssets($assets)
            ->withExitAssetHandover($exitAssetHandover);
    }
}
