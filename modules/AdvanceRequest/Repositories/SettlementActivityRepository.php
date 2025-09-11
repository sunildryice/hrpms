<?php

namespace Modules\AdvanceRequest\Repositories;

use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\SettlementActivity;

use DB;

class SettlementActivityRepository extends Repository
{
    public function __construct(
        SettlementActivity $settlementActivity
    ){
        $this->model = $settlementActivity;
    }

    

      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $settlementActivity = $this->model->create($inputs);
            DB::commit();
            return $settlementActivity;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


   
}
