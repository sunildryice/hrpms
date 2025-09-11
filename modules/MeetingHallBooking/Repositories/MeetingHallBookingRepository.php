<?php

namespace Modules\MeetingHallBooking\Repositories;

use App\Repositories\Repository;
use DateTime;
use Modules\MeetingHallBooking\Models\MeetingHallBooking;

use DB;

class MeetingHallBookingRepository extends Repository
{
    public function __construct(MeetingHallBooking $meetingHallBooking)
    {
        $this->model = $meetingHallBooking;
    }

    public function getBookings()
    {
        return $this->model->whereHas('meetingHall', function($q) {
                                $q->where('office_id', auth()->user()->employee->office_id);
                            })
                            ->where('meeting_date', '=', now()->format('Y-m-d') )
                            ->where('status_id', config('constant.SUBMITTED_STATUS'))
                            ->get();
    }

    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.CANCELLED_STATUS');
            $meetingHallBooking = $this->model->find($id);
            $meetingHallBooking->update($inputs);
            DB::commit();
            return $meetingHallBooking;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $meetingHallBooking = $this->model->create($inputs);
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'status_id' => 3,
                ];
                $this->forward($meetingHallBooking->id, $forwardInputs);
            }
            DB::commit();
            return $meetingHallBooking;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $meetingHallBooking = $this->model->findOrFail($id);
            $inputs['status_id'] = 3;
            $meetingHallBooking->update($inputs);
            DB::commit();
            return $meetingHallBooking;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $meetingHallBooking = $this->model->find($id);
            $meetingHallBooking->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'status_id' => 3,
                ];
                $this->forward($meetingHallBooking->id, $forwardInputs);
            }
            DB::commit();
            return $meetingHallBooking;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
