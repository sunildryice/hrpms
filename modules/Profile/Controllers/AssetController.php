<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;


class AssetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param GoodRequestAssetRepository $goodRequestAssets
     * @param UserRepository $users
     */
    public function __construct(
        GoodRequestAssetRepository   $goodRequestAssets,
        UserRepository             $users
    )
    {
        $this->goodRequestAssets = $goodRequestAssets;
        $this->users = $users;
    }

    /**
     * Display a listing of the Good requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {

        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequestAssets->with(['asset','submittedLog', 'approver'])
                ->where('assigned_user_id', $authUser->id)
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
                })->addColumn('status', function($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if($authUser->can('handover', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('assets.handover.create', $row->id) . '"';
                        $btn .= ' rel="tooltip" title="Start Handover Asset">';
                        $btn .= '<i class="bi bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Profile::Asset.index');
    }
}
