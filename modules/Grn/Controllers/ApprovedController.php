<?php

namespace Modules\Grn\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;


class ApprovedController extends Controller
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
        protected EmployeeRepository        $employees,
        protected FiscalYearRepository      $fiscalYears,
        protected GrnRepository $grns,
        protected UserRepository            $users
    )
    {
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
            $data = $this->grns->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('received_date', function ($row) {
                    return $row->getReceivedDate();
                })->addColumn('order_number', function ($row) {
                    return $row->getGrnableNumber();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('grn_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.grns.show', $row->id) . '" rel="tooltip" title="View GRN">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('grns.print', $row->id) . '" rel="tooltip" title="Print GRN"><i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Grn::Approved.index');
    }

    /**
     * Show the specified grn.
     *
     * @param $grnId
     * @return mixed
     */
    public function show($grnId)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($grnId);
        $this->authorize('viewApproved', $grn);

        return view('Grn::Approved.show')
            ->withAuthUser($authUser)
            ->withGrn($grn);

    }
}
