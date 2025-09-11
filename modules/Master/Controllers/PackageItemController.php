<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\PackageItemRepository;
use Modules\Master\Repositories\PackageRepository;
use Modules\Master\Repositories\UnitRepository;
use Modules\Master\Requests\Package\Item\StoreRequest;
use Modules\Master\Requests\Package\Item\UpdateRequest;

class PackageItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ItemRepository $items
     * @param PackageRepository $packages
     * @param PackageItemRepository $packageItems
     * @param UnitRepository $units
     */
    public function __construct(
        ItemRepository $items,
        PackageRepository $packages,
        PackageItemRepository $packageItems,
        UnitRepository $units,

    ) {
        $this->items = $items;
        $this->packages = $packages;
        $this->packageItems = $packageItems;
        $this->units = $units;
    }

    /**
     * Display a listing of the package items
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $id)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $package = $this->packages->find($id);
            $data = $this->packageItems->select(['*'])->with(['item', 'unit'])
                ->where('package_id', $id)->get();
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            // if ($authUser->can('update', $package)) {
            $datatable->addColumn('action', function ($row) use ($authUser, $package) {
                $btn = '<button data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                $btn .= route('master.packages.items.edit', [$row->package_id, $row->id]) . '"><i class="bi-pencil-square"></i></button>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('master.packages.items.destroy', [$row->package_id, $row->id]) . '">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            });
            // }
            return $datatable->addColumn('item', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new package item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $items = $this->items->getActiveItems();
        $package = $this->packages->find($id);
        return view('Master::PrPackage.Item.create')
            ->withItems($items)
            ->withPackage($package);
    }

    /**
     * Store a newly created package item in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $package = $this->packages->find($id);
        $inputs = $request->validated();
        $inputs['package_id'] = $package->id;
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $packageItem = $this->packageItems->create($inputs);

        if ($packageItem) {
            return response()->json(['status' => 'ok',
                'packageItem' => $packageItem,
                'packageItemCount' => $packageItem->package->packageItems()->count(),
                'message' => 'package item is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'package item can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified package item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $packageItem = $this->packageItems->find($id);
        $items = $this->items->getActiveItems();
        return view('Master::PrPackage.Item.edit')
            ->withItems($items)
            ->withPackageItem($packageItem)
            ->withUnits($packageItem->item->units->whereNotNull('activated_at'));
    }

    /**
     * Update the specified package item in storage.
     *
     * @param UpdateRequest $request
     * @param $prId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $packageItem = $this->packageItems->find($id);
        $inputs = $request->validated();
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $packageItem = $this->packageItems->update($id, $inputs);
        if ($packageItem) {
            return response()->json(['status' => 'ok',
                'purchaseRequestItem' => $packageItem,
                'packageItemCount' => $packageItem->package->packageItems()->count(),
                'message' => 'package item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'package item can not be updated.'], 422);
    }

    /**
     * Remove the specified package item from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($pkgId, $id)
    {
        $package = $this->packages->find($pkgId);
        $packageItem = $this->packageItems->find($id);
        $flag = $this->packageItems->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'packageItemCount' => $package->packageItems()->count(),
                'message' => 'package item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'package item can not deleted.',
        ], 422);
    }
}
