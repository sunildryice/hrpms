<?php

namespace Modules\EventCompletion\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\EventCompletion\Requests\Participant\StoreRequest;
use Modules\EventCompletion\Requests\Participant\UpdateRequest;
use Modules\EventCompletion\Repositories\EventCompletionRepository;
use Modules\EventCompletion\Repositories\EventParticipantRepository;



class EventParticipantController extends Controller{

    private $eventParticipants;
    private $eventCompletion;

    public function __construct(
        EventParticipantRepository $eventParticipants ,
        EventCompletionRepository $eventCompletion   
        )
    {
        $this->eventParticipants = $eventParticipants;
        $this->eventCompletion = $eventCompletion;
    }

    public function index(Request $request,$eventCompletionId){
        $authUser = auth()->user();
        
        if($request->ajax()){
            $eventCompletion = $this->eventCompletion->find($eventCompletionId);
            $data = $this->eventParticipants->where('event_completion_id','=',$eventCompletionId)
                    ->get();
            $datatable = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name',function ($row){
                        return $row->name;
                    })->addColumn('office',function ($row){
                        return $row->office;
                    })->addColumn('designation',function ($row){
                        return $row->designation;
                    })->addColumn('contact',function ($row){
                        return $row->contact;
                    })->addColumn('action',function ($row) use ($authUser,$eventCompletion){
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-participants-modal-form" href="';
                        $btn .= route('event.completion.participants.edit', [$row->event_completion_id, $row->id]) . '" rel="tooltip" title="Edit Event Participant"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('event.completion.participants.destroy', [$row->event_completion_id, $row->id]) . '" rel="tooltip" title="Delete Event Participant">';
                        $btn .= '<i class="bi-trash"></i></a>';
                        return $btn;
                    })->rawColumns(['action'])
                    ->make(true);
            return $datatable;
        }
        return true;
    }

    public function create($id){
        $eventCompletion = $this->eventCompletion->find($id);
        return view('EventCompletion::Participant.create')
                ->withEventCompletion($eventCompletion);
    }

    public function store(StoreRequest $request,$id){
        $eventCompletion = $this->eventCompletion->find($id);
        $inputs = $request->validated();
        $inputs['event_completion_id'] = $id;
        $eventParticipant = $this->eventParticipants->create($inputs);
        if($eventParticipant){
            return response()->json([
                'status' => 'ok',
                'message' => 'Event Participant successfully created',
                'eventParticipant' => $eventParticipant,
                'participantCount' => $eventParticipant->eventCompletion->participants()->count(),   

            ],200);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Event Participant failed to create'
            ],422
        );
    }

    public function edit($ecId,$id){
        $eventCompletion = $this->eventCompletion->find($ecId);
        $eventParticipant = $this->eventParticipants->find($id);
        return view('EventCompletion::Participant.edit')
                ->withEventCompletion($eventCompletion)
                ->withEventParticipant($eventParticipant);
    }

    public function update(UpdateRequest $request,$ecId,$id){
        $eventParticipant = $this->eventParticipants->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $eventParticipant = $this->eventParticipants->update($id,$inputs);
        if($eventParticipant){
            return response()->json([
                'status' => 'success',
                'message' => 'Event Participant successfully updated',
                'eventParticipant' => $eventParticipant,
                'participantCount' => $eventParticipant->eventCompletion->participants()->count(),   
            ],200);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Event Participant failed to update'
            ],200
        );
    }

    public function destroy($ecId,$id){
        $eventCompletion = $this->eventCompletion->find($ecId);
        $eventParticipant = $this->eventParticipants->find($id);
        $flag = $this->eventParticipants->destroy($id);
        if($flag){
            return response()->json([
                'status' => 'success',
                'message' => 'Event Participant successfully deleted',
                'participantCount' => $eventCompletion->participants->count()   
            ],200);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Event Participant failed to delete'
            ],422
        );
    }
}