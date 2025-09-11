<?php

namespace Modules\Master\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ActivityCodeRepository;

class ActivityCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  ActivityCodeRepository $activityCodes
     * @return void
     */
    public function __construct(
        ActivityCodeRepository $activityCodes
    )
    {
        $this->activityCodes = $activityCodes;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'activityCodes'=>$this->activityCodes->get()
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
        $activityCode = $this->activityCodes->find($id);
        return response()->json([
            'accountCodes'=>$activityCode->accountCodes()->whereNotNull('activated_at')->orderBy('title')->get(),
            'activityCode'=>$activityCode
        ], 200);
    }
}
