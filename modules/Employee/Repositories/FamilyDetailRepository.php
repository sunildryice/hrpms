<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\FamilyDetail;
use DB;

class FamilyDetailRepository extends Repository
{
    public function __construct(
        Employee $employee,
        FamilyDetail $familyDetail
    )
    {
        $this->employee = $employee;
        $this->model = $familyDetail;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $employee = $this->employee->find($inputs['employee_id']);
            if(array_key_exists('nominee_at', $inputs)){
                $this->model->whereNotNull('nominee_at')->update(['nominee_at'=>NULL]);
            }
            $familyMember = $this->model->create($inputs);
            DB::commit();
            return $familyMember;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $familyMember = $this->model->findOrFail($id);
            if(array_key_exists('nominee_at', $inputs)){
                $this->model->whereNotNull('nominee_at')->update(['nominee_at'=>NULL]);
            }
            $familyMember->fill($inputs)->save();
            DB::commit();
            return $familyMember;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
