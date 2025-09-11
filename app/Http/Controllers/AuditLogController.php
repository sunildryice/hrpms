<?php

namespace App\Http\Controllers;

use Modules\Privilege\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\AuditLogRepository;
use DataTables;

class AuditLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AuditLogRepository $logs
     * @param UserRepository $users
     * @return void
     */
    public function __construct(
        AuditLogRepository $logs,
        UserRepository     $users
    )
    {
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
            $data = $this->logs->with(['user'])->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->getUserFullNameAndEmail();
                })->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    $btn .= route('audit.logs.show', $row->id) . '" rel="tooltip" title="View Log"><i class="bi-eye"></i></a>';

                    return $btn;
                })->rawColumns(['action'])->make(true);
        }

        return view('logs.audit');
    }

    /**
     * Display the specified log.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $log = $this->logs->find($id);
        return view('logs.audit-detail')
            ->withLog($log);
    }
}
