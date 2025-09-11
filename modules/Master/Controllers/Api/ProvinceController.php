<?php

namespace Modules\Master\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ProvinceRepository;

class ProvinceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  ProvinceRepository $provinces
     * @return void
     */
    public function __construct(
        ProvinceRepository $provinces
    )
    {
        $this->provinces = $provinces;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'provinces'=>$this->provinces->select(['*'])->orderBy('province_name')->get()
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
        $province = $this->provinces->find($id);
        return response()->json([
            'districts'=>$province->districts()->orderBy('district_name')->get(),
            'province'=>$province
        ], 200);
    }
}
