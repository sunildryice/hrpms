<?php

namespace Modules\EventCompletion\Repositories;

use App\Repositories\Repository;

use DB;
use Modules\EventCompletion\Models\EventParticipant;

class EventParticipantRepository extends Repository
{
    public function __construct(
        EventParticipant $eventParticipant
    )
    {
        $this->model = $eventParticipant;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = $this->model->create($inputs);
            DB::commit();
            return $eventParticipant;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = $this->model->findOrFail($id);
            $eventParticipant->fill($inputs)->save();
            DB::commit();
            return $eventParticipant;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = $this->model->findOrFail($id);
            $eventParticipant->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
