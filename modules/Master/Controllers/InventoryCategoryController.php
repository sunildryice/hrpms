<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\InventoryCategoryRepository;
use Modules\Master\Repositories\InventoryTypeRepository;
use Modules\Master\Requests\InventoryCategory\StoreRequest;
use Modules\Master\Requests\InventoryCategory\UpdateRequest;

use DataTables;

class InventoryCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InventoryCategoryRepository $inventoryCategories
     * @param InventoryTypeRepository $inventoryTypes
     * @return void
     */
    public function __construct(
        InventoryCategoryRepository $inventoryCategories,
        InventoryTypeRepository $inventoryTypes
    )
    {
        $this->inventoryCategories = $inventoryCategories;
        $this->inventoryTypes = $inventoryTypes;
    }

    /**
     * Display a listing of the inventory category.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->inventoryCategories->with(['inventoryType'])->select([
                'id','inventory_type_id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-inventory-modal-form" href="';
                    $btn .= route('master.inventory.categories.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.inventory.categories.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('inventory_type', function ($row) {
                    return $row->getInventoryType();
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::InventoryCategory.index');
    }

    /**
     * Show the form for creating a new inventory category.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::InventoryCategory.create')
            ->withInventoryTypes($this->inventoryTypes->get());
    }

    /**
     * Store a newly created inventory category in storage.
     *
     * @param \Modules\Master\Requests\InventoryCategory\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inventoryCategory = $this->inventoryCategories->create($inputs);
        if ($inventoryCategory) {
            return response()->json([
                'inventoryCategory' => $inventoryCategory,
                'message' => 'Inventory category is successfully added.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Inventory category can not be added.'
        ], 422);
    }

    /**
     * Display the specified inventory category.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $inventoryCategory = $this->inventoryCategories->find($id);
        return response()->json(['status' => 'ok', 'inventoryCategory' => $inventoryCategory], 200);
    }

    /**
     * Show the form for editing the specified inventory category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $inventoryCategory = $this->inventoryCategories->find($id);
        return view('Master::InventoryCategory.edit')
            ->withInventoryCategory($inventoryCategory)
            ->withInventoryTypes($this->inventoryTypes->get());
    }

    /**
     * Update the specified inventory category in storage.
     *
     * @param \Modules\Master\Requests\InventoryCategory\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inventoryCategory = $this->inventoryCategories->update($id, $inputs);
        if ($inventoryCategory) {
            return response()->json(['status' => 'ok',
                'inventoryCategory' => $inventoryCategory,
                'message' => 'Inventory category is successfully updated.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Inventory category can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified inventory category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->inventoryCategories->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Inventory category is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Inventory category can not deleted.',
        ], 422);
    }
}
