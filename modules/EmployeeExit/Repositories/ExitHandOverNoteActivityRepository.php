<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitHandOverNoteActivity;

use DB;

class ExitHandOverNoteActivityRepository extends Repository
{
    public function __construct(
        ExitHandOverNoteActivity $exitHandOverNoteActivity
    ){
        $this->model = $exitHandOverNoteActivity;
    }


    
      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            // $inputs['status_id'] = 1;
            $exitHandOverNoteActivity = $this->model->create($inputs);
            DB::commit();
            return $exitHandOverNoteActivity;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


}
