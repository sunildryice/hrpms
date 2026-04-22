<?php

namespace Modules\OffDayWork\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;

class InvolvedController extends Controller
{
    public function __construct(
        protected OffDayWorkRepository $offDayWork
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->offDayWork->with(['logs', 'status', 'requester.employee'])
                ->whereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                })->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })->addColumn('project', function ($row) {
                    return implode(', ', $row->getProjectNames()) ?: '-';
                })->editColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })->editColumn('date', function ($row) {
                    return $row->getOffDayWorkDate();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('off.day.work.show', $row->id) . '" title="View Off Day Work Request"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('OffDayWork::involved.index');
    }
}
