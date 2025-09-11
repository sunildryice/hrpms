<?php

namespace Modules\DistributionRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\DistributionRequest\Repositories\DistributionHandoverItemRepository;
use Modules\DistributionRequest\Repositories\DistributionHandoverRepository;


use DataTables;

class DistributionHandoverItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistributionHandoverItemRepository $distributionHandoverItems
     * @param DistributionHandoverRepository $distributionHandovers
     */
    public function __construct(
        DistributionHandoverItemRepository $distributionHandoverItems,
        DistributionHandoverRepository     $distributionHandovers,
    )
    {
        $this->distributionHandoverItems = $distributionHandoverItems;
        $this->distributionHandovers = $distributionHandovers;
    }

    /**
     * Display a listing of the distribution handover items
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $distributionHandoverId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);
            $data = $this->distributionHandoverItems->select(['*'])
                ->with(['activityCode', 'accountCode', 'donorCode', 'item', 'unit'])
                ->whereDistributionHandoverId($distributionHandover->id);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            return $datatable->addColumn('activity', function ($row) {
                return $row->activityCode->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->accountCode->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->donorCode->getDonorCode();
            })->addColumn('item_name', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnit();
            })->make(true);
        }
        return true;
    }
}
