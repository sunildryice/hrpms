<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\BillCategoryRepository;
use Modules\Master\Requests\BillCategory\StoreRequest;
use Modules\Master\Requests\BillCategory\UpdateRequest;

use DataTables;

class BillCategoryController extends Controller
{
    /**
     * The bill type repository instance.
     *
     * @var BillCategoryRepository
     */
    protected $billCategories;

    /**
     * Create a new controller instance.
     *
     * @param BillCategoryRepository $billCategories
     * @return void
     */
    public function __construct(
        BillCategoryRepository $billCategories
    )
    {
        $this->billCategories = $billCategories;
    }

    /**
     * Display a listing of the bill type.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->billCategories->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-bill-categories-modal-form" href="';
                    $btn .= route('master.bill.categories.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.bill.categories.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::BillCategory.index');
    }

    /**
     * Show the form for creating a new bill type.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::BillCategory.create');
    }

    /**
     * Store a newly created bill type in storage.
     *
     * @param \Modules\Master\Requests\BillCategory\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $billCategory = $this->billCategories->create($inputs);
        if ($billCategory) {
            return response()->json([
                'billCategory' => $billCategory,
                'message' => 'Bill category is successfully added.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Bill category can not be added.'
        ], 422);
    }

    /**
     * Display the specified bill type.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $billCategory = $this->billCategories->find($id);
        return response()->json(['status' => 'ok', 'billCategory' => $billCategory], 200);
    }

    /**
     * Show the form for editing the specified bill type.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $billCategory = $this->billCategories->find($id);
        return view('Master::BillCategory.edit')
            ->withBillCategory($billCategory);
    }

    /**
     * Update the specified bill type in storage.
     *
     * @param \Modules\Master\Requests\BillCategory\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $billCategory = $this->billCategories->update($id, $inputs);
        if ($billCategory) {
            return response()->json(['status' => 'ok',
                'billCategory' => $billCategory,
                'message' => 'Bill category is successfully updated.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Bill category can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified bill category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->billCategories->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Bill category is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Bill category can not deleted.',
        ], 422);
    }
}
