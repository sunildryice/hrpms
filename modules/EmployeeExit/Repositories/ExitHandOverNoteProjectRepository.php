<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitHandOverNoteProject;

use DB;

class ExitHandOverNoteProjectRepository extends Repository
{
    public function __construct(
        ExitHandOverNoteProject $exitHandOverNoteProject
    ){
        $this->model = $exitHandOverNoteProject;
    }


    
      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            // $inputs['status_id'] = 1;
            $exitHandOverNoteProject = $this->model->create($inputs);
            DB::commit();
            return $exitHandOverNoteProject;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


}
