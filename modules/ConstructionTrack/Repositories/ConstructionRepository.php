<?php

namespace Modules\ConstructionTrack\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Modules\ConstructionTrack\Models\Construction;

use Illuminate\Support\Facades\DB;

/**
 * @method Construction find()
 */
class ConstructionRepository extends Repository
{
    public function __construct(
        Construction $construction
    ){
        $this->model = $construction;
    }

    public function getConstructionTrackNumber()
    {
        $max = $this->model->max('advance_number') + 1;
        return $max;
    }


    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $construction = $this->model->create($inputs);
            if (array_key_exists('donor_codes', $inputs)) {
                $construction->donors()->sync($inputs['donor_codes']);
            }
            DB::commit();
            return $construction;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $construction = $this->model->find($id);
            if($inputs['btn'] == 'submit') {
                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            }
            $construction->fill($inputs)->save();
            if (array_key_exists('donor_codes', $inputs)) {
                $construction->donors()->sync($inputs['donor_codes']);
            }
            DB::commit();
            return $construction;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $construction = $this->model->findOrFail($id);
            $construction->constructionParties()->delete();
            foreach ($construction->constructionProgresses()->get() as $progress) {
                $progress->logs()->delete();
                $progress->attachments()->delete();
            }
            $construction->constructionProgresses()->delete();
            foreach ($construction->constructionInstallments()->get() as $installment) {
                $installment->logs()->delete();
            }
            $construction->constructionInstallments()->delete();
            $construction->delete();
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}
