<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ActivityCode;

use DB;

class ActivityCodeRepository extends Repository
{
    public function __construct(ActivityCode $activityCode)
    {
        $this->model = $activityCode;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $activityCode = $this->model->create($inputs);
            $activityCode->accountCodes()->sync($inputs['account_codes']);
            DB::commit();
            return $activityCode;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getActiveActivityCodes()
    {
        return $this->model->select(['id', 'title','description'])
            ->whereNotNull('activated_at')
            ->orderBy('title', 'asc')->get();
    }


    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $activityCode = $this->model->findOrFail($id);
            $activityCode->fill($inputs)->save();
            $activityCode->accountCodes()->sync($inputs['account_codes']);
            DB::commit();
            return $activityCode;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
