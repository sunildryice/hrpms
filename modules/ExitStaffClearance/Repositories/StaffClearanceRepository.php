<?php

namespace Modules\ExitStaffClearance\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\Master\Repositories\FiscalYearRepository;

class StaffClearanceRepository extends Repository
{
    public function __construct(StaffClearance $staffClearance,
        protected FiscalYearRepository $fiscalYear,
        protected StaffClearanceRecordRepository $staffClearanceAnswers,
        protected StaffClearanceDepartmentRepository $clearanceDepartments,
    ) {
        $this->model = $staffClearance;
    }

    public function updateOrCreateRecords($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $staffClearance = $this->model->findOrFail($id);
            foreach ($inputs['clearance'] as $departmentId => $recordInput) {
                $staffClearance->records()->updateOrCreate([
                    'clearance_department_id' => $departmentId,
                ], [
                    'created_by' => $inputs['created_by'],
                    'cleared_at' => isset($recordInput['check']) ? now() : null,
                    'remarks' => $recordInput['remarks'],
                ]);
            }
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $staffClearance = $this->model->findOrFail($id);
            $staffClearance->fill($inputs)->save();
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $staffClearance = $this->model->findOrFail($id);
            $staffClearance->logs()->delete();
            $staffClearance->records()->delete();
            $staffClearance->delete();
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function verify($staffClearanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['supervisor_id'] = $inputs['user_id'] = auth()->id();
            $inputs['verified_at'] = date('Y-m-d H:i:s');
            $staffClearance = $this->model->findOrFail($staffClearanceId);
            $staffClearance->update($inputs);
            $staffClearance->logs()->create($inputs);
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function certify($staffClearanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['certifier_id'] = $inputs['user_id'] = auth()->id();
            $inputs['certified_at'] = date('Y-m-d H:i:s');
            $staffClearance = $this->model->findOrFail($staffClearanceId);
            $staffClearance->update($inputs);
            $staffClearance->logs()->create($inputs);
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function endorse($staffClearanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['user_id'] = auth()->id();
            $inputs['endorsed_at'] = date('Y-m-d H:i:s');
            $staffClearance = $this->model->findOrFail($staffClearanceId);
            $staffClearance->update($inputs);
            $staffClearance->logs()->create($inputs);
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function approve($staffClearanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['user_id'] = auth()->id();
            $inputs['approved_at'] = date('Y-m-d H:i:s');
            $staffClearance = $this->model->findOrFail($staffClearanceId);
            $staffClearance->update($inputs);
            $staffClearance->logs()->create($inputs);
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function recommend($staffClearanceId, $inputs)
    {
        DB::beginTransaction();
        try {
            $staffClearance = $this->model->findOrFail($staffClearanceId);
            $staffClearance->update($inputs);
            $staffClearance->logs()->create($inputs);
            DB::commit();

            return $staffClearance;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getPendingClearances()
    {
        $departmentCount = $this->clearanceDepartments->select('*')->where('parent_id', '<>', '0')->count();

        return $this->model->select('*')->with('records')
            ->where(function ($q) use ($departmentCount) {
                $q->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.VERIFIED_STATUS')]);
                    // ->orWhere(function ($q) use ($departmentCount) {
                    //     $q->whereHas('records', function ($q) use ($departmentCount) {
                    //         $q->havingRaw('COUNT(*) < ?', [$departmentCount]);
                    //         $q->whereNotNull('cleared_at');
                    //     });
                    // });
            })
            ->whereHas('employee', function($q) {
                $q->where('office_id', auth()->user()?->employee?->office_id);
                $q->whereHas('user', function($q) {
                    $q->where('id', '<>', auth()->id());
                });
            })
            ->get();
    }
}
