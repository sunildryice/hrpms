<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class TimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets
    ) {
        $this->destinationPath = 'TimeSheet';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->timeSheets->query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('timesheet_date', function($row){
                    return $row->timesheet_date?->format('M d, Y');
                })
                ->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ';
                        $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }
                    return $attachment;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm open-timesheet-modal-form" href="';
                    $btn .= route('timesheet.show', $row->id) . '" rel="tooltip" title="View TimeSheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
    
                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-timesheet-modal-form" href="';
                    $btn .= route('timesheet.edit', $row->id) . '" rel="tooltip" title="Edit TimeSheet">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';
    
                    $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"';
                    $btn .= ' data-href="' . route('timesheet.destroy', $row->id) . '" rel="tooltip"';
                    $btn .= ' title="Delete TimeSheet"><i class="bi bi-trash"></i></a>';
    
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'attachment'])
                ->make(true);
        }


        return view('Project::TimeSheet.index');
    }


}