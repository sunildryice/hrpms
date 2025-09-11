<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\Privilege\Repositories\UserRepository;

class AssetController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected GoodRequestAssetRepository $goodRequestAssets,
        protected UserRepository $users,
        protected EmployeeRepository $employees
    ) {
    }

    /**
     * Display a listing of the Good requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $employee)
    {

        if ($request->ajax()) {
            $employee = $this->employees->find($employee);
            $data = $this->goodRequestAssets->with(['asset', 'submittedLog', 'approver'])
                ->where('assigned_user_id', $employee->user?->id)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('approver', function ($row) {
                    return $row->getApproverName();
                })
                ->addColumn('asset_number', function ($row) {
                    return $row->getAssetNumber();
                })->addColumn('old_asset_code', function ($row) {
                    return $row->asset->old_asset_code;
                })->addColumn('assigned_user', function ($row) {
                    return $row->getAssignedUser();
                })->addColumn('department', function ($row) {
                    return $row->getAssignedDepartment();
                })->addColumn('office', function ($row) {
                    return $row->getAssignedOffice();
                })->addColumn('item_name', function ($row) {
                    return $row->asset->inventoryItem->getItemName();
                })->addColumn('assigned_on', function ($row) {
                    return $row->getCreatedDate();
                })->addColumn('condition', function ($row) {
                    return $row->getCondition();
                })->addColumn('remarks', function ($row) {
                    return $row->getRemarks();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->rawColumns(['status'])
                ->make(true);
        }
    }
}
