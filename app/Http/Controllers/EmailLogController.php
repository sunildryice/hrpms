<?php

namespace App\Http\Controllers;

use App\Repositories\EmailLogRepository;
use Modules\Privilege\Repositories\UserRepository;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmailLogRepository  $logs
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(
        EmailLogRepository $logs,
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
        return view('logs.email')
            ->withLogs($this->logs->searchAndPaginate($request->all(), 100))
            ->withUsers($this->users->orderby('full_name', 'asc')->pluck('full_name', 'id'))
            ->withRequestData($request->all());
    }
}
