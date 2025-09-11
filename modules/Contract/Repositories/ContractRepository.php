<?php
namespace Modules\Contract\Repositories;

use App\Repositories\Repository;
use Modules\Contract\Models\Contract;

use DB;

class ContractRepository extends Repository
{
    public function __construct(Contract $contract)
    {
        $this->model = $contract;
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $contract = $this->model->find($id);
            $inputs['amendment_number'] = $contract->amendments->max('amendment_number') + 1;
            $contract->amendments()->create($inputs);
            DB::commit();
            return $contract;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
