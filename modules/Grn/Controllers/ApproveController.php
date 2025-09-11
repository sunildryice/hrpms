<?php

namespace Modules\Grn\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Grn\Notifications\GrnApproved;
use Modules\Grn\Notifications\GrnRejected;
use Modules\Grn\Notifications\GrnReturned;
use Modules\Grn\Notifications\GrnSubmitted;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\Grn\Requests\Approve\StoreRequest;
use DataTables;


class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GrnRepository $grns
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        GrnRepository $grns,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->grns = $grns;
        $this->users = $users;
    }

    /**
     * Display a listing of the grns
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->grns->with(['fiscalYear', 'status', 'createdBy'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('order_date', function ($row) {
                    return $row->getOrderDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('order_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.grns.create', $row->id) . '" rel="tooltip" title="Approve GRN">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Grn::Approve.index');
    }

    public function create($grnId)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($grnId);
        $this->authorize('approve', $grn);

        $latestTenure = $grn->createdBy->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name'])
            ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        return view('Grn::Approve.create')
            ->withAuthUser($authUser)
            ->withGrn($grn)
            ->withSupervisors($supervisors);
    }

    public function store(StoreRequest $request, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('approve', $grn);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $grn = $this->grns->approve($grn->id, $inputs);

        if ($grn) {
            $message = '';
            if ($grn->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'GRN is successfully returned.';
                $grn->createdBy->notify(new GrnReturned($grn));
            } else {
                $message = 'GRN is successfully approved.';
                $grn->createdBy->notify(new GrnApproved($grn));
            }

            return redirect()->route('approve.grns.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('GRN can not be approved.');
    }
}
