<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitHandOverNoteDocument;

use DB;

class ExitHandOverNoteDocumentRepository extends Repository
{
    public function __construct(
        ExitHandOverNoteDocument $exitHandOverNoteDocument
    ){
        $this->model = $exitHandOverNoteDocument;
    }


    
      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            // $inputs['status_id'] = 1;
            $exitHandOverNoteDocument = $this->model->create($inputs);
            DB::commit();
            return $exitHandOverNoteDocument;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


}
