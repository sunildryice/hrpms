<?php

namespace Modules\MeetingHallBooking\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\MeetingHallRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\MeetingHallBooking\Repositories\MeetingHallBookingRepository;
use Modules\MeetingHallBooking\Requests\StoreRequest;
use Modules\MeetingHallBooking\Requests\UpdateRequest;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

class MeetingHallBookingController extends Controller
{
    protected $meetingHallBooking;

    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository  $employees  ,
     * @param  MeetingHallBookingRepository  $meetingHallBooking  ,
     * @param  MeetingHallRepository  $meetingHall  ,
     * @param  RoleRepository  $roles  ,
     * @param  StatusRepository  $status  ,
     */
    public function __construct(
        EmployeeRepository $employees,
        MeetingHallBookingRepository $meetingHallBooking,
        MeetingHallRepository $meetingHall,
        RoleRepository $roles,
        StatusRepository $status,
        UserRepository $user
    ) {
        $this->employees = $employees;
        $this->meetingHallBooking = $meetingHallBooking;
        $this->meetingHall = $meetingHall;
        $this->roles = $roles;
        $this->status = $status;
        $this->user = $user;
        $this->destinationPath = 'meetingHallBooking';
    }

    /**
     * Display a listing of the meeting hall booking request by employee id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->meetingHallBooking
                ->with(['meetingHall'])
                ->select('*')
                ->where(function ($q) use ($userId) {
                    $q->where('created_by', $userId);
                })->orWhere(function ($q) {
                    $q->where('status_id', 3);
                })->orderBy('meeting_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('meeting_hall', function ($row) {
                    return $row->getMeetingHall();
                })
                ->addColumn('meeting_date', function ($row) {
                    return $row->meeting_date->toFormattedDateString();
                })
                ->addColumn('start_time', function ($row) {
                    return $row->getStartTime();
                })
                ->addColumn('end_time', function ($row) {
                    return $row->getEndTime();
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('booked_by', function ($row) {
                    return $row->getBookedBy();
                })
                ->addColumn('purpose', function ($row) {
                    return $row->purpose;
                })
                ->addColumn('remarks', function ($row) {
                    // return Str::limit($row->remarks, 50);
                    return $row->remarks;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('meeting.hall.bookings.edit', $row->id).'" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    } elseif ($authUser->can('cancel', $row)) {
                        $btn .= '<a href = "javascript:;" class="btn btn-danger btn-sm cancel-meeting-booking"';
                        $btn .= 'data-confirm = "Do you want to cancel this booking?"';
                        $btn .= 'data-href = "'.route('meeting.hall.bookings.cancel.store', $row->id).'" title="Cancel Booking">';
                        $btn .= '<i class="bi bi-x-circle" ></i></a>';

                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-outline-danger btn-sm cancel-meeting-booking"';
                        $btn .= 'data-confirm = "Do you want to amend this booking?"';
                        $btn .= 'data-href = "'.route('meeting.hall.bookings.amend', $row->id).'" title="Amend">';
                        $btn .= '<i class="bi bi-arrow-clockwise" ></i></a>';
                    } elseif ($authUser->can('reverseCancel', $row)) {
                        $btn .= '<a href = "javascript:;" class="btn btn-warning btn-sm cancel-meeting-booking"';
                        $btn .= 'data-confirm = "Do you want to revert cancellation of this booking?"';
                        $btn .= 'data-href = "'.route('meeting.hall.bookings.cancel.reverse', $row->id).'" title="Reverse Cancel">';
                        $btn .= '<i class="bi bi-x-circle" ></i></a>';
                    }

                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('MeetingHallBooking::index');
    }

    /**
     * Show the form for creating a new meeting hall request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        //        $this->authorize('manage-meeting-hall');
        $authUser = auth()->user();
        $office_id = $this->employees->find($authUser->employee_id)->latestTenure->office_id;
        $meetingHall = $this->meetingHall->select('*')->where('office_id', $office_id)->get();

        return view('MeetingHallBooking::create')
            ->withMeetingHalls($meetingHall);

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['status_id'] = 1;
        if ($inputs['btn'] == 'submit') {
            $checkTime = $this->checkTime($inputs);

            if ($checkTime) {
                return redirect()->back()->withInput()
                    ->withWarningMessage('Requested Meeting Hall is already booked in this time slot.');
            } else {
                $inputs['status_id'] = 3;
            }

        }
        $meetingHallBooking = $this->meetingHallBooking->create($inputs);

        if ($meetingHallBooking) {
            return redirect()->route('meeting.hall.bookings.index')
                ->withSuccessMessage('Meeting Hall Booking request successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Meeting Hall Booking request can not be added.');
    }

    private function checkTime($inputs)
    {
        $i = 0;
        $bookingExists = false;
        $meeting_date = $inputs['meeting_date'];
        $meeting_hall_id = $inputs['meeting_hall_id'];
        $start_time = $inputs['start_time'];
        $end_time = $inputs['end_time'];

        $previousBookings = $this->meetingHallBooking
            ->select(['id', 'meeting_date', 'start_time', 'end_time'])
            ->where(function ($q) use ($meeting_date, $meeting_hall_id) {
                $q->where('meeting_hall_id', $meeting_hall_id);
                $q->where('meeting_date', $meeting_date);
                $q->where('status_id', 3);
            })->orderBy('created_at', 'desc')
            ->get();

        foreach ($previousBookings as $key => $previousBooking) {
            if (($previousBooking->start_time <= $start_time && $start_time <= $previousBooking->end_time)
                || ($previousBooking->start_time <= $end_time && $end_time <= $previousBooking->end_time)) {
                $bookingExists = true;
            }
            $i++;
        }

        return $bookingExists;
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);

        return view('MeetingHallBooking::edit')
            ->withMeetingHallBooking($meetingHallBooking)
            ->withMeetingHalls($this->meetingHall->get());
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Modules\Employee\Requests\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);
        $this->authorize('update', $meetingHallBooking);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($inputs['btn'] == 'submit') {
            $checkTime = $this->checkTime($inputs);
            if ($checkTime) {
                return redirect()->back()->withInput()
                    ->withWarningMessage('Requested Meeting Hall is already booked in this time slot.');
            } else {
                $inputs['status_id'] = 3;
            }
        }
        $meetingHallBooking = $this->meetingHallBooking->update($id, $inputs);
        if ($meetingHallBooking) {
            return redirect()->route('meeting.hall.bookings.index')
                ->withSuccessMessage('Meeting Hall Booking request successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Meeting Hall Booking request can not be updated.');
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);
        $this->authorize('delete', $meetingHallBooking);
        $flag = $this->meetingHallBooking->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Meeting Hall Booking Request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Meeting Hall Booking Request can not deleted.',
        ], 422);
    }

    /**
     * Cancel the specified meeting hall booking from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel($id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);
        $this->authorize('cancel', $meetingHallBooking);
        $flag = $this->meetingHallBooking->cancel($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Meeting Hall Booking Request is successfully cancelled.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Meeting Hall Booking Request can not cancelled.',
        ], 422);
    }

    public function reverseCancel($id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);
        $this->authorize('reverseCancel', $meetingHallBooking);
        $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
        $inputs['btn'] = 'save';
        $flag = $this->meetingHallBooking->update($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Cancel reverted for Meeting Hall booking',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Meeting Hall Booking Request cancellation cannot be reverted',
        ], 422);
    }

    public function amend($id)
    {
        $meetingHallBooking = $this->meetingHallBooking->find($id);
        $this->authorize('cancel', $meetingHallBooking);
        $inputs['status_id'] = config('constant.CREATED_STATUS');
        $inputs['btn'] = 'save';
        $flag = $this->meetingHallBooking->update($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Amendment successfull',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Meeting Hall Booking Request cannot be amended',
        ], 422);
    }
}
