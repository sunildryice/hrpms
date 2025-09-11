<?php

namespace Modules\EventCompletion\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Modules\EventCompletion\Repositories\EventCompletionRepository;

class ApprovedController extends Controller
{
    protected $eventCompletion;

    public function __construct(EventCompletionRepository $eventCompletion)
    {
        $this->eventCompletion = $eventCompletion;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if($request->ajax()){
            $data = $this->eventCompletion->getApproved();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('start_date',function($row){
                return $row->getStartDate();
            })
            ->addColumn('end_date',function($row){
                return $row->getEndDate();
            })
            ->addColumn('venue',function($row){
                return $row->getVenue();
            })->addColumn('district',function($row){
                return $row->district->getDistrictName();
            })->addColumn('requester',function($row){
                return $row->getRequesterName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function($row) use($authUser){
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.event.completion.show', $row->id) . '" rel="tooltip" title="View Event Completion Report">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('event.completion.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
            })
            ->rawColumns(['action','status'])
            ->make(true);
        }
        return view('EventCompletion::Approved.index');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $eventCompletion = $this->eventCompletion->find($id);
        $requester = $eventCompletion->requester->employee;
        return view('EventCompletion::Approved.show')
            ->withEventCompletion($eventCompletion)
            ->withRequester($requester);
    }

    public function print($id)
    {
        $eventCompletion = $this->eventCompletion->find($id);
        $requester = $eventCompletion->requester->employee;
        return view('EventCompletion::Approved.print')
                ->withEventCompletion($eventCompletion)
                ->withRequester($requester);
    }
}
