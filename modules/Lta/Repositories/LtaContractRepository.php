<?php
namespace Modules\Lta\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Lta\Models\LtaContract;

class LtaContractRepository extends Repository
{
    public function __construct(LtaContract $ltaContract)
    {
        $this->model = $ltaContract;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $ltaContract = $this->model->find($id);
            $ltaContract->ltaItems()->delete();
            $ltaContract->delete();
            DB::commit();
            return true;
        }catch(\Illuminate\Database\QueryException $e){
            DB::rollback();
            return false;
        }
    }
}
