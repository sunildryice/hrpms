<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityLogRepository;
use Modules\Privilege\Repositories\UserRepository;
use Illuminate\Http\Request;

use DataTables;

class ActivityLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  ActivityLogRepository  $logs
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(
        ActivityLogRepository $logs,
        UserRepository $users
    ){
        $this->logs = $logs;
        $this->users = $users;
    }

    /**
     * Display a listing of the log.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->logs->with(['user'])->select(['*']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->getUserNameEmail();
                })->make(true);
        }

        return view('logs.activity');
    }
}
