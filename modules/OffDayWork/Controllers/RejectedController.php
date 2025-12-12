<?php

namespace Modules\OffDayWork\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\OffDayWork\Repositories\OffDayWorkLogRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Yajra\DataTables\DataTables;

class RejectedController extends Controller
{

    public function __construct(
        protected OffDayWorkRepository $offDayWork,
        protected OffDayWorkLogRepository $offDayWorkLog,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->offDayWork
                ->where('status_id', '=', config('constant.REJECTED_STATUS'))
                ->where('approver_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return  $row->getRequestDate();
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->addColumn('employee', function ($row) {
                    return $row->requester->employee->full_name ?? $row->requester->full_name ?? '-';
                })
                ->editColumn('date', function ($row) {
                    return $row->getOffDayWorkDate();
                })
                ->addColumn('project', function ($row) {
                    return $row->project->title ?? '-';
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {

                    $authUser = auth()->user();
                    $btn = '<a href="' . route('rejected.off.day.work.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('OffDayWork::rejected.index');
    }

    public function show($offDayWork)
    {
        $offDayWork = $this->offDayWork->find($offDayWork);
        return view('OffDayWork::rejected.show', compact('offDayWork'));
    }
}
