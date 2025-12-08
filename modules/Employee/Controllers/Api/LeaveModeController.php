<?php

namespace Modules\Employee\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Repositories\LeaveModeRepository;

class LeaveModeController extends Controller
{

    public function __construct(protected LeaveModeRepository $leaveModeRepository) {}

    public function index(Request $request, $leaveTypeId)
    {
        if (
            $leaveTypeId == config('constant.SICK_LEAVE') ||
            $leaveTypeId == config('constant.ANNUAL_LEAVE')
        ) {
            $leaveModes = $this->leaveModeRepository
                ->where('title', '!=', 'No Leave')->get();
        } else {
            $leaveModes = $this->leaveModeRepository
                ->where('title', '!=', 'No Leave')
                ->whereIn('title', ['Full Day'])
                ->get();
        }


        return response()->json([
            'leaveModes' => $leaveModes,
        ]);
    }
}
