<?php

namespace Modules\AdvanceRequest\Repositories;

use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\AdvanceRequestDetail;

use DB;

class AdvanceRequestDetailsRepository extends Repository
{
    public function __construct(
        AdvanceRequestDetail $advanceRequestDetail
    ){
        $this->model = $advanceRequestDetail;
    }

    public function getAdvanceRequestNumber()
    {
        $max = $this->model->max('advance_request_number') + 1;
        return $max;
    }


      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $advanceRequestDetail = $this->model->create($inputs);
            DB::commit();
            return $advanceRequestDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


   
}
