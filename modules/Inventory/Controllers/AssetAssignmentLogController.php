<?php

namespace Modules\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Inventory\Repositories\AssetAssignLogRepository;
use Modules\Inventory\Repositories\AssetRepository;
use Yajra\DataTables\DataTables;
use Modules\Master\Repositories\AssetStatusRepository;
use Modules\Privilege\Repositories\UserRepository;

class AssetAssignmentLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AssetRepository $assets
     */
    public function __construct(
        AssetAssignLogRepository $assetAssignLogs,
        AssetRepository $assets,
        EmployeeRepository $employees,
        UserRepository $users,
        AssetStatusRepository $assetStatuses
    )
    {
        $this->assets = $assets;
        $this->assetAssignLogs = $assetAssignLogs;
        $this->employees = $employees;
        $this->users = $users;
        $this->assetStatuses = $assetStatuses;
    }

    /**
     * Display a listing of the asset condition log
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $asset)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');

        if ($request->ajax()) {
            $asset = $this->assets->find($asset);
            $data = $this->assetAssignLogs->with(['assignedUser'])->where('asset_id', $asset->id)->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('assigned_user', function ($row) {
                    return $row->getAssignedUser();
                })->addColumn('assigned_office', function ($row) {
                    return $row->getAssignedOffice();
                })->addColumn('assigned_department', function ($row) {
                    return $row->getAssignedDepartment();
                })->addColumn('assigned_district', function ($row) {
                    return $row->getAssignedDistrict();
                })->addColumn('assigned_date', function ($row) {
                    return $row->getCreatedDate();
                })->addColumn('condition', function ($row) {
                    return $row->getCondition();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    return $btn;
                })->rawColumns(['action', 'asset_status'])
                ->make(true);
        }

        return view('Inventory::Asset.AssetAssignmentLog.index');
    }
}
