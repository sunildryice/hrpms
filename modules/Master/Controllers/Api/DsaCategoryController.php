<?php

namespace Modules\Master\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DsaCategoryRepository;

class DsaCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  DsaCategoryRepository $dsaCategories
     * @return void
     */
    public function __construct(
        DsaCategoryRepository $dsaCategories
    )
    {
        $this->dsaCategories = $dsaCategories;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'dsaCategories'=>$this->dsaCategories->select(['*'])->get()
        ], 200);
    }

    /**
     * Display the specified province.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = $this->dsaCategories->find($id);
        return response()->json([
            'dsaCategory'=>$category,
        ], 200);
    }
}
