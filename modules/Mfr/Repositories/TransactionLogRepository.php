<?php

namespace Modules\Mfr\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeAttendance\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Mfr\Models\TransactionLog;

class TransactionLogRepository extends Repository
{
    public function __construct(TransactionLog $transactionLog)
    {
        $this->model = $transactionLog;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $transactionLog = $this->model->create($inputs);
            DB::commit();
            return $transactionLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transactionLog = $this->model->findOrFail($id);
            $transactionLog->fill($inputs)->save();
            DB::commit();
            return $transactionLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transactionLog = $this->model->findOrFail($id);
            $transactionLog->delete();
            DB::commit();
            return $transactionLog;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}
