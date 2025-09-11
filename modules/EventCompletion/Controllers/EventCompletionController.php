<?php

namespace Modules\EventCompletion\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EventCompletion\Notifications\EventCompletionCancelled;
use Modules\EventCompletion\Notifications\EventCompletionSubmitted;
use Modules\EventCompletion\Repositories\EventCompletionRepository;
use Modules\EventCompletion\Repositories\EventParticipantRepository;
use Modules\EventCompletion\Requests\StoreRequest;
use Modules\EventCompletion\Requests\UpdateRequest;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\Facades\DataTables;

class EventCompletionController extends Controller
{
    private $eventCompletion;
    private $districts;
    private $activityCodes;
    private $user;
    public function __construct(
        EventCompletionRepository $eventCompletion,
        DistrictRepository $districts,
        ActivityCodeRepository $activityCodes,
        UserRepository $user,
        EventParticipantRepository $eventParticipants,
    ) {
        $this->eventCompletion = $eventCompletion;
        $this->districts = $districts;
        $this->activityCodes = $activityCodes;
        $this->user = $user;
        $this->eventParticipants = $eventParticipants;

    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $inputs = $this->eventCompletion->with(['requester', 'logs'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
                $q->orWhere('original_user_id', $authUser->id);
            })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($inputs)
                ->addIndexColumn()
                ->addColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })
                ->addColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })
                ->addColumn('venue', function ($row) {
                return $row->getVenue();
            })->addColumn('district', function ($row) {
                return $row->district->getDistrictName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {

                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('event.completion.show', $row->id) . '" rel="tooltip" title="View Event Completion"><i class="bi-eye"></i></a>';
                if ($authUser->can('update', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('event.completion.edit', $row->id) . '" rel="tooltip" title="Edit Event Completion"><i class="bi-pencil-square"></i></a>';
                } else {
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('event.completion.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('cancel', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm cancel-event-completion"';
                        $btn .= 'data-href = "' . route('event.completion.cancel.store', $row->id) . '" title="Cancel Event Completion Report">';
                        $btn .= '<i class="bi bi-x-circle" ></i></a>';
                    }
                }
                if ($authUser->can('delete', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('event.completion.destroy', $row->id) . '"  rel="tooltip" title="Delete Event Completion">';
                    $btn .= '<i class="bi-trash3"></i></a>';
                }

                return $btn;
            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('EventCompletion::index');
    }

    public function create()
    {
        $authUser = auth()->user();
        // $this->authorize('create', EventCompletion::class);
        $districts = $this->districts->getEnabledDistricts();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        return view('EventCompletion::create')
            ->withDistricts($districts)
            ->withActivityCodes($activityCodes);
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        // $checkExist = $this->eventCompletion->select(['*'])
        //     ->where('requester_id', $authUser->id)
        //     ->whereNotIn('status_id', [config('constant.REJECTED_STATUS'), config('constant.CANCELLED_STATUS')])
        //     ->where(function ($q) use ($inputs) {
        //         $q->where('program_date', $inputs['program_date']);
        //         $q->where('activity_code_id', $inputs['activity_code_id']);
        //         $q->where('district_id', $inputs['district_id']);
        //     })->first();
        // if ($checkExist) {
        //     return redirect()->back()->withInput()->withWarningMessage(['Event Already Exists']);
        // }
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $eventCompletion = $this->eventCompletion->create($inputs);
        if ($eventCompletion) {
            return redirect()->route('event.completion.edit', $eventCompletion->id)
                ->withSuccessMessage('Event Completion Report added Successfully');
        }
        return redirect()->back()->withInput()->withWarningMessage('Event Completion Report can not be added');
    }

    public function edit($id)
    {
        $authUser = auth()->user();
        $eventCompletion = $this->eventCompletion->find($id);
        $this->authorize('update', $eventCompletion);
        $districts = $this->districts->getEnabledDistricts();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $approvers = $this->user->permissionBasedUsers('approve-event-completion');
        return view('EventCompletion::edit')
            ->withEventCompletion($eventCompletion)
            ->withDistricts($districts)
            ->withActivityCodes($activityCodes)
            ->withAuthUser($authUser)
            ->withApprovers($approvers);
    }

    public function update(UpdateRequest $request, $id)
    {

        $authUser = auth()->user();
        $eventCompletion = $this->eventCompletion->find($id);
        $this->authorize('update', $eventCompletion);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $eventCompletion = $this->eventCompletion->update($id, $inputs);
        if ($eventCompletion) {
            $message = 'Event Completion Updated Successfully';
            $route = redirect()->route('event.completion.edit', $eventCompletion->id);
            if ($eventCompletion->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Event Completion Submitted Successfully';
                $eventCompletion->approver->notify(new EventCompletionSubmitted($eventCompletion));
                $route = redirect()->route('event.completion.index');
            }
            return $route->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()->withWarningMessage(['Event Completion can not be updated.']);
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $eventCompletion = $this->eventCompletion->find($id);
        return view('EventCompletion::show')
            ->withAuthUser($authUser)
            ->withEventCompletion($eventCompletion);
    }

    public function destroy($id)
    {
        $eventCompletion = $this->eventCompletion->find($id);
        $this->authorize('delete', $eventCompletion);
        $flag = $this->eventCompletion->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Event Ecompletion Report is deleted successfully',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Event Completion Report can not be deleted,',
        ], 422);
    }

    public function cancel($id)
    {
        $eventCompletion = $this->eventCompletion->find($id);
        $this->authorize('cancel', $eventCompletion);
        $inputs = [
            'user_id' => auth()->id(),
            'log_remarks' => 'Event Completion Report is cacelled',
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $eventCompletion = $this->eventCompletion->cancel($id, $inputs);
        if ($eventCompletion) {
            if ($eventCompletion->status_id == config('constant.CANCELLED_STATUS')) {
                if ($eventCompletion->reviewer_id) {
                    $eventCompletion->reviewer->notify(new EventCompletionCancelled($eventCompletion));
                }
                if ($eventCompletion->approver_id && $eventCompletion->reviewer_id != $eventCompletion->approver_id) {
                    $eventCompletion->approver->notify(new EventCompletionCancelled($eventCompletion));
                }
            }
            return response()->json([
                'type' => 'success',
                'message' => 'Event is cancelled successfully',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Event Completion can no be cancelled',
        ], 422);
    }

}
