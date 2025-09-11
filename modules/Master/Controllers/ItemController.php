<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\InventoryCategoryRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;
use Modules\Master\Requests\Item\StoreRequest;
use Modules\Master\Requests\Item\UpdateRequest;

use DataTables;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InventoryCategoryRepository $categories
     * @param ItemRepository $items
     * @param UnitRepository $units
     * @return void
     */
    public function __construct(
        InventoryCategoryRepository $categories,
        ItemRepository $items,
        UnitRepository $units
    )
    {
        $this->categories = $categories;
        $this->items = $items;
        $this->units = $units;
    }

    /**
     * Display a listing of the item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->items->select([
                'id', 'inventory_category_id', 'title', 'item_code', 'created_by', 'updated_at'
            ])->with(['category', 'createdBy'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('master.items.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.items.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('category', function ($row) {
                    return $row->getCategory();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::Item.index');
    }

    /**
     * Show the form for creating a new item.
     *
     * @return mixed
     */
    public function create()
    {
        return view('Master::Item.create')
            ->withCategories($this->categories->get())
            ->withUnits($this->units->get());
    }

    /**
     * Store a newly created item in storage.
     *
     * @param \Modules\Master\Requests\Item\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inputs['units'] = $request->units ?: [];
        $item = $this->items->create($inputs);
        if ($item) {
            return response()->json(['status' => 'ok',
                'item' => $item,
                'message' => 'Item is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Item can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $item = $this->items->find($id);
        return view('Master::Item.edit')
            ->withCategories($this->categories->get())
            ->withItem($item)
            ->withUnits($this->units->get());
    }

    /**
     * Update the specified item in storage.
     *
     * @param \Modules\Master\Requests\Item\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['units'] = $request->units ?: [];
        $item = $this->items->update($id, $inputs);
        if ($item) {
            return response()->json(['status' => 'ok',
                'item' => $item,
                'message' => 'Item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Item can not be updated.'], 422);
    }

    /**
     * Remove the specified item from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->items->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Item can not deleted.',
        ], 422);
    }
}
