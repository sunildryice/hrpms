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
                ->addColumn('action', function ($row) use ($authUser) {
                    // $btn = '<a class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    // $btn .= route('activity-stage.show', $row->id) . '" rel="tooltip" title="View Activity Stage">';
                    // $btn .= '<i class="bi bi-eye"></i></a>';

                    // $btn .= ' <a class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    // $btn .= route('activity-stage.edit', $row->id) . '" rel="tooltip" title="Edit Activity Stage">';
                    // $btn .= '<i class="bi bi-pencil-square"></i></a>';

                    // $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"';
                    // $btn .= ' data-href="' . route('activity-stages.destroy', $row->id) . '" rel="tooltip"';
                    // $btn .= ' title="Delete Activity Stage"><i class="bi bi-trash"></i></a>';

                    // return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }


        return view('Project::Timesheet.index');
    }

   
}