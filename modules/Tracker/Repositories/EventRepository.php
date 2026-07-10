<?php

namespace Modules\Tracker\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Tracker\Models\Event;

class EventRepository extends Repository
{
    public function __construct(
        Event $event
    )
    {
        $this->model = $event;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->create($inputs);
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->fill($inputs)->save();
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->accompanyingMembers()->sync([]);
            $record->roasters()->delete();
            $record->delete();
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}
