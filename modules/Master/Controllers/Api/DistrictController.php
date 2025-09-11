<?php

namespace Modules\Master\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DistrictRepository;

class DistrictController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  DistrictRepository $districts
     * @return void
     */
    public function __construct(
        DistrictRepository $districts
    )
    {
        $this->districts = $districts;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'districts'=>$this->districts->select(['*'])->orderBy('district_name')->get()
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
        $district = $this->districts->find($id);
        return response()->json([
            'district'=>$district,
            'localLevels'=>$district->localLevels()->orderBy('local_level_name')->get(),
        ], 200);
    }
}
