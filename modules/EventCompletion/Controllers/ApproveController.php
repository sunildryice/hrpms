<?php

namespace Modules\EventCompletion\Controllers;

use DataTables;

use Illuminate\Http\Request;



use App\Http\Controllers\Controller;
use Modules\Privilege\Repositories\UserRepository;
use Modules\EventCompletion\Requests\Approve\StoreRequest;
use Modules\EventCompletion\Notifications\EventCompletionApproved;
use Modules\EventCompletion\Notifications\EventCompletionRejected;
use Modules\EventCompletion\Notifications\EventCompletionReturned;
use Modules\EventCompletion\Notifications\EventCompletionSubmitted;
use Modules\EventCompletion\Repositories\EventCompletionRepository;

class ApproveController extends Controller
{

    private $eventCompletion;
    private $user;
  public function __construct(
    EventCompletionRepository $eventCompletion,
    UserRepository $user
  ){
    $this->eventCompletion = $eventCompletion;
    $this->user = $user;
  }

  public function index(Request $request){
    $authUser = auth()->user();
    if($request->ajax()){
      $inputs = $this->eventCompletion->with(['requester','status'])->select('*')
                      ->where('approver_id',$authUser->id)
                      ->whereIn('status_id',[config('constant.SUBMITTED_STATUS'),config('constant.RECOMMENDED_STATUS')])
                      ->orderBy('created_at','desc')
                      ->get();
      return DataTables::of($inputs)
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
      })
      ->addColumn('status', function ($row) {
          return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
      })->addColumn('action', function($row) use($authUser){
        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
        $btn .= route('approve.event.completion.create', $row->id) . '" rel="tooltip" title="Approve ECR">';
        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
        return $btn;
          return $btn;
      })
      ->rawColumns(['action','status'])
      ->make(true);
    }
    return view('EventCompletion::Approve.index');
  }

  public function create($id){
    $authUser = auth()->user();
    $eventCompletion = $this->eventCompletion->find($id);
    $this->authorize('approve', $eventCompletion);
    $approvers = $this->user->permissionBasedUsers('approve-recommended-event-completion');
    return view('EventCompletion::Approve.create')
      ->withEventCompletion($eventCompletion)
      ->withApprovers($approvers);
  }

  public function store(StoreRequest $request, $ecId){
    $inputs = $request->validated();
    $eventCompletion = $this->eventCompletion->find($ecId);
    $this->authorize('approve',$eventCompletion);
    $inputs['user_id'] = auth()->id();
    $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
    $eventCompletion = $this->eventCompletion->approve($eventCompletion->id,$inputs);
    if($eventCompletion){
        $message = '';
        if($eventCompletion->status_id == config('constant.RETURNED_STATUS')){
            $message = 'Event Completion Report is successfully returned';
            $eventCompletion->requester->notify(new EventCompletionReturned($eventCompletion));
        }else if($eventCompletion->status_id == config('constant.REJECTED_STATUS')){
            $message = 'Event Completion Report is rejected';
            $eventCompletion->requester->notify(new EventCompletionRejected($eventCompletion));
        }else if($eventCompletion->status_id == config('constant.RECOMMENDED_STATUS')){
            $message = 'Event Completion Report is successfully recommended';
            $eventCompletion->approver->notify(new EventCompletionSubmitted($eventCompletion));
        }else{
            $message = 'Event Completion Report is successfully approved';
            $eventCompletion->requester->notify(new EventCompletionApproved($eventCompletion));
        }

        return redirect()->route('approve.event.completion.index')
            ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Event Completion Report can not be approved.');
    }
}
