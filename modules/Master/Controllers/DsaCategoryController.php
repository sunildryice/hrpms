<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DsaCategoryRepository;
use Modules\Master\Requests\DsaCategory\StoreRequest;
use Modules\Master\Requests\DsaCategory\UpdateRequest;

use DataTables;

class DsaCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DsaCategoryRepository $dsaCategories
     * @return void
     */
    public function __construct(
        DsaCategoryRepository $dsaCategories
    )
    {
        $this->dsaCategories = $dsaCategories;
    }

    /**
     * Display a listing of the dsa category.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->dsaCategories->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-dsa-modal-form" href="';
                    $btn .= route('master.dsa.categories.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.dsa.categories.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::DsaCategory.index')
            ->withDsaCategorys($this->dsaCategories->all());
    }

    /**
     * Show the form for creating a new dsa category.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::DsaCategory.create');
    }

    /**
     * Store a newly created dsa category in storage.
     *
     * @param \Modules\Master\Requests\DsaCategory\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $dsaCategory = $this->dsaCategories->create($inputs);
        if ($dsaCategory) {
            return response()->json(['status' => 'ok',
                'dsaCategory' => $dsaCategory,
                'message' => 'DSA category is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'DSA category can not be added.'], 422);
    }

    /**
     * Display the specified dsa category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dsaCategory = $this->dsaCategories->find($id);
        return response()->json(['status' => 'ok', 'dsaCategory' => $dsaCategory], 200);
    }

    /**
     * Show the form for editing the specified dsa category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $dsaCategory = $this->dsaCategories->find($id);
        return view('Master::DsaCategory.edit')
            ->withDsaCategory($dsaCategory);
    }

    /**
     * Update the specified dsa category in storage.
     *
     * @param \Modules\Master\Requests\DsaCategory\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $dsaCategory = $this->dsaCategories->update($id, $inputs);
        if ($dsaCategory) {
            return response()->json(['status' => 'ok',
                'dsaCategory' => $dsaCategory,
                'message' => 'DSA category is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'DSA category can not be updated.'], 422);
    }

    /**
     * Remove the specified dsa category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->dsaCategories->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'DSA category is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'DSA category can not deleted.',
        ], 422);
    }
}
