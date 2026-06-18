<?php

namespace Modules\Tracker\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Tracker\Models\Risk;

class RiskRepository extends Repository
{
    public function __construct(
        Risk $risk
    )
    {
        $this->model = $risk;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $risk = $this->model->create($inputs);
            DB::commit();
            return $risk;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $risk = $this->model->findOrFail($id);
            $risk->fill($inputs)->save();
            DB::commit();
            return $risk;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $risk = $this->model->findOrFail($id);
            $risk->delete();
            DB::commit();
            return $risk;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}
