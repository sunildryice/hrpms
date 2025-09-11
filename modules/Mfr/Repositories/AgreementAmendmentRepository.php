<?php

namespace Modules\Mfr\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Mfr\Models\AgreementAmendment;

class AgreementAmendmentRepository extends Repository
{
    public function __construct(
        AgreementAmendment $agreement,
    ) {
        $this->model = $agreement;

    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->fill($data)->save();
            DB::commit();
            return $record;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            // dd($e);
            return false;
        }
    }

}
