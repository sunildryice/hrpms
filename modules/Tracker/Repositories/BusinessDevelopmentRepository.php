<?php

namespace Modules\Tracker\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Tracker\Models\BusinessDevelopment;

class BusinessDevelopmentRepository extends Repository
{
    public function __construct(
        BusinessDevelopment $businessDevelopment
    )
    {
        $this->model = $businessDevelopment;
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

            if ($record->attachment && Storage::exists($record->attachment)) {
                Storage::delete($record->attachment);
            }

            $record->delete();
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}
