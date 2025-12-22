<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Holiday;

use DB;

class HolidayRepository extends Repository
{
    public function __construct(Holiday $holiday)
    {
        $this->model = $holiday;
    }

    public function getHolidaysByDate()
    {
        return $this->model->orderBy('holiday_date', 'desc')->get();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $holiday = $this->model->create($inputs);
            if (array_key_exists('office_ids', $inputs)) {
                $holiday->offices()->sync($inputs['office_ids']);
            }
            DB::commit();
            return $holiday;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $holiday = $this->model->findOrFail($id);
            $holiday->fill($inputs)->save();
            $holiday->offices()->detach();
            if (array_key_exists('office_ids', $inputs)) {
                $holiday->offices()->sync($inputs['office_ids']);
            }
            DB::commit();
            return $holiday;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
